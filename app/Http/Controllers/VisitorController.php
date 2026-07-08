<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use Carbon\Carbon;

class VisitorController extends Controller
{
    public function track(Request $request)
    {
        $request->validate([
            'visitor_id'   => 'required|string|max:64',
            'page_url'     => 'nullable|string|max:255',
            'referrer_url' => 'nullable|string|max:255',
            'name'         => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
        ]);

        $ip = $request->ip();

        // Parse device/browser/os from User-Agent
        $ua          = $request->userAgent() ?? '';
        $deviceType  = $this->detectDevice($ua);
        $browser     = $this->detectBrowser($ua);
        $os          = $this->detectOS($ua);

        // Geo (optional: use geoip package later)
        // For now just store IP, country/city can be added via MaxMind

        $visitor = Visitor::updateOrCreate(
            ['visitor_id' => $request->visitor_id],
            [
                'ip_address'    => $ip,
                'last_page_url' => $request->page_url,
                'referrer_url'  => $request->referrer_url,
                'device_type'   => $deviceType,
                'browser'       => $browser,
                'os'            => $os,
                'last_seen_at'  => Carbon::now(),
            ]
        );

        // Increment visit count on each track call
        $visitor->increment('visit_count');

        // Update name/email if provided
        if ($request->filled('name'))  $visitor->update(['name'  => $request->name]);
        if ($request->filled('email')) $visitor->update(['email' => $request->email]);

        return response()->json([
            'success'    => true,
            'visitor_id' => $visitor->visitor_id,
            'db_id'      => $visitor->id,
        ]);
    }

    // ── Helpers ─────────────────────────────────

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