<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorPage;
use App\Models\TrustedDomain;
use App\Models\User;
use Carbon\Carbon;
use App\Jobs\ResolveVisitorLocation;

class VisitorController extends Controller
{

    private function extractSource($url)
    {
        if (!$url) return null;

        if (str_contains($url, 'google')) return 'Google';
        if (str_contains($url, 'facebook')) return 'Facebook';
        if (str_contains($url, 'bing')) return 'Bing';

        return parse_url($url, PHP_URL_HOST);
    }

    public function track(Request $request)
    {
        $request->validate([
            'visitor_id'   => 'required|string|max:64',
            'site_id'      => 'required|string|max:64',
            'domain_id'    => 'nullable|string|max:255',       // ★ NEW
            'page_url'     => 'nullable|string|max:500',
            'referrer_url' => 'nullable|string|max:255',
            'name'         => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
        ]);

        $user = User::where('site_id', $request->site_id)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid site ID'], 403);
        }

        // ★ page_url iframe se real parent URL bhejta hai — usi se domain nikalo
        $pageUrl = $request->input('page_url', '');

        if ($pageUrl) {
            if (!str_starts_with($pageUrl, 'http')) {
                $pageUrl = 'https://' . $pageUrl;
            }
            $host          = parse_url($pageUrl, PHP_URL_HOST) ?? '';
            $requestDomain = TrustedDomain::normalizeDomain($host);
        } else {
            $origin = $request->header('Origin') ?? $request->header('Referer') ?? '';
            if (!$origin) {
                return response()->json(['error' => 'Missing origin'], 403);
            }
            if (!str_starts_with($origin, 'http')) {
                $origin = 'https://' . $origin;
            }
            $host          = parse_url($origin, PHP_URL_HOST) ?? '';
            $requestDomain = TrustedDomain::normalizeDomain($host);
        }

        $isTrusted = TrustedDomain::isTrusted($user->site_id, $requestDomain);

        $isLocalhost = in_array($requestDomain, ['localhost', '127.0.0.1', '::1'])
            || str_ends_with($requestDomain, '.test')
            || str_ends_with($requestDomain, '.local');

        if (!$isTrusted && !$isLocalhost) {
            return response()->json([
                'error' => 'Unauthorized domain'
            ], 403);
        }

        $ip = $request->ip();
        $ua = $request->userAgent() ?? '';

        // ★ Resolve domain_id: use explicit value first, then derive from page_url
        $domainId = $request->input('domain_id');
        if (!$domainId && $pageUrl) {
            $host = parse_url($pageUrl, PHP_URL_HOST) ?? '';
            if ($host) {
                $domainId = str_replace('.', '_', $host);
            }
        }
        if (!$domainId && !$pageUrl) {
            $origin = $request->header('Origin') ?? $request->header('Referer') ?? '';
            if ($origin) {
                $host = parse_url($origin, PHP_URL_HOST) ?? '';
                if ($host) {
                    $domainId = str_replace('.', '_', $host);
                }
            }
        }

        $visitor = Visitor::updateOrCreate(
            ['visitor_id' => $request->visitor_id],
            [
                'site_id'       => $request->site_id,
                'domain_id'     => $domainId,                   // ★ NEW
                'ip_address'    => $ip,
                'referrer_url'  => $request->referrer_url,
                'device_type'   => $this->detectDevice($ua),
                'browser'       => $this->detectBrowser($ua),
                'os'            => $this->detectOS($ua),
                'last_seen_at'  => Carbon::now(),
            ]
        );

        // If visitor was "left_website", reset to browsing when they come back
        if ($visitor->status === 'left_website') {
            $visitor->update(['status' => 'browsing']);
            $visitor->increment('visit_count');
        }

        // ✅ Dispatch location job only if location not resolved yet
        if (!$visitor->country) {
            ResolveVisitorLocation::dispatchSync($visitor->visitor_id, $ip);
            $visitor->refresh();
        }

        if ($request->filled('name'))  $visitor->update(['name'  => $request->name]);
        if ($request->filled('email')) $visitor->update(['email' => $request->email]);

        // ★ Always keep domain_id updated (in case it changed or was null before)
        if ($domainId && $visitor->domain_id !== $domainId) {
            $visitor->update(['domain_id' => $domainId]);
        }

        return response()->json([
            'success'    => true,
            'visitor_id' => $visitor->visitor_id,
        ]);
    }

    /**
     * Visitor ne page open kiya — status = browsing (agar chatting nahi hai)
     */
    public function pageStart(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|string|max:64',
            'url'        => 'required|string|max:500',
            'title'      => 'nullable|string|max:500',
        ]);

        $visitor = Visitor::where('visitor_id', $request->visitor_id)->first();

        if ($visitor) {
            $updateData = [
                'last_page_url'   => $request->url,
                'last_page_title' => $request->title,
                'last_seen_at'    => now(),
            ];

            if ($visitor->status !== 'chatting') {
                $updateData['status'] = 'browsing';
            }

            // ★ Keep domain_id updated from page URL
            if ($request->url && !$visitor->domain_id) {
                $host = parse_url($request->url, PHP_URL_HOST) ?? '';
                if ($host) {
                    $updateData['domain_id'] = str_replace('.', '_', $host);
                }
            }

            $visitor->update($updateData);
        }

        VisitorPage::create([
            'visitor_id' => $request->visitor_id,
            'url'        => $request->url,
            'title'      => $request->title ?? null,
            'time_spent' => 0,
            'visited_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Page pe time spent update karo
     */
    public function trackPage(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|string',
            'url'        => 'required|string',
            'title'      => 'nullable|string',
            'time_spent' => 'required|integer',
        ]);

        $timeSpent = (int) $request->time_spent;
        if ($timeSpent < 2) return response()->json(['ignored' => true]);

        // Find the latest record for this visitor and URL
        $updated = VisitorPage::where('visitor_id', $request->visitor_id)
            ->where('url', $request->url)
            ->orderBy('id', 'desc')
            ->first();

        if ($updated) {
            // Only update if the new time is greater than the saved time
            if ($timeSpent > $updated->time_spent) {
                $updated->update(['time_spent' => $timeSpent]);
            }
        } else {
            VisitorPage::create([
                'visitor_id' => $request->visitor_id,
                'url'        => $request->url,
                'title'      => $request->title,
                'time_spent' => $timeSpent,
                'visited_at' => now(),
            ]);
        }

        // Update last_seen_at + domain_id if missing
        $visitorUpdate = ['last_seen_at' => now()];

        $visitor = Visitor::where('visitor_id', $request->visitor_id)->first();
        if ($visitor && !$visitor->domain_id && $request->url) {
            $host = parse_url($request->url, PHP_URL_HOST) ?? '';
            if ($host) {
                $visitorUpdate['domain_id'] = str_replace('.', '_', $host);
            }
        }

        Visitor::where('visitor_id', $request->visitor_id)->update($visitorUpdate);

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|string|max:64',
            'site_id'    => 'required|string|max:64',
            'status'     => 'required|string|in:browsing,chatting',
        ]);

        $visitor = Visitor::where('visitor_id', $request->visitor_id)
            ->where('site_id', $request->site_id)
            ->first();

        if ($visitor) {
            $visitor->update([
                'status'       => $request->status,
                'last_seen_at' => now(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Visitor ne chat widget open karke message bheja — status = chatting
     */
    public function chatStart(Request $request)
    {
        $visitor = Visitor::where('visitor_id', $request->visitor_id)->first();

        if ($visitor) {
            $visitor->update([
                'status'       => 'chatting',
                'last_seen_at' => now(),
            ]);
            $visitor->increment('chat_count');
        }

        return response()->json(['success' => true]);
    }

    public function assignAgent(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|string',
            'agent_id'   => 'required|integer',
        ]);

        $visitor = Visitor::where('visitor_id', $request->visitor_id)->firstOrFail();

        $visitor->update([
            'assign_userID' => (int) $request->agent_id,
            'status'        => 'chatting',
        ]);

        // \Log::info('[VisitorController] Agent assigned', [
        //     'visitor_id' => $request->visitor_id,
        //     'agent_id' => $request->agent_id,
        //     'updated_assign_userID' => $visitor->fresh()->assign_userID
        // ]);

        return response()->json([
            'success' => true,
            'visitor_id' => $visitor->visitor_id,
            'assign_userID' => $visitor->assign_userID
        ]);
    }

    /**
     * Visitor ne website leave ki — status = left_website
     */
    public function leave(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|string|max:64',
        ]);

        Visitor::where('visitor_id', $request->visitor_id)
            ->update([
                'status'       => 'left_website',
                'last_seen_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Heartbeat — visitor still on website
     */
    public function heartbeat(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|string|max:64',
        ]);

        Visitor::where('visitor_id', $request->visitor_id)
            ->update(['last_seen_at' => now()]);

        return response()->json(['ok' => true]);
    }

    // ── Device detection helpers ────────────────────────────────

    private function detectDevice(string $ua): string
    {
        if (preg_match('/mobile|android|iphone|ipod/i', $ua)) return 'mobile';
        if (preg_match('/tablet|ipad/i', $ua))               return 'tablet';
        return 'desktop';
    }

    private function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg'))     return 'Edge';
        if (str_contains($ua, 'OPR'))     return 'Opera';
        if (str_contains($ua, 'Chrome'))  return 'Chrome';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Safari'))  return 'Safari';
        return 'Unknown';
    }

    private function detectOS(string $ua): string
    {
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Mac'))     return 'macOS';
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        if (str_contains($ua, 'Linux'))   return 'Linux';
        return 'Unknown';
    }
}