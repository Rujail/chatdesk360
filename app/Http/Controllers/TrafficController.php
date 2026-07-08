<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorPage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TrafficController extends Controller
{
    public function index()
    {
        return view('traffic.index');
    }

    /**
     * Live visitors list
     */
    public function live()
    {
        $user   = Auth::user();
        $cutoff = Carbon::now()->subMinutes(5);
        $leftCutoff = Carbon::now()->subMinutes(2);

        $visitors = Visitor::where('site_id', $user->site_id)
            ->where(function ($q) use ($cutoff, $leftCutoff) {
                $q->where(function ($q2) use ($cutoff) {
                    $q2->whereIn('status', ['browsing', 'chatting'])
                       ->where('last_seen_at', '>=', $cutoff);
                })->orWhere(function ($q2) use ($leftCutoff) {
                    $q2->where('status', 'left_website')
                       ->where('last_seen_at', '>=', $leftCutoff);
                });
            })
            ->orderByRaw("FIELD(status, 'chatting', 'browsing', 'left_website')")
            ->orderBy('last_seen_at', 'desc')
            ->get();

        $visitorIds = $visitors->pluck('visitor_id');

        // ── Total time per visitor (from previous pages) ──
        $totalTimes = VisitorPage::whereIn('visitor_id', $visitorIds)
            ->selectRaw('visitor_id, SUM(time_spent) as total_time')
            ->groupBy('visitor_id')
            ->pluck('total_time', 'visitor_id');

        // ★ Get the start time of the current page for each visitor
        $currentPageStarts = VisitorPage::whereIn('visitor_id', $visitorIds)
            ->selectRaw('visitor_id, MAX(visited_at) as page_start')
            ->groupBy('visitor_id')
            ->pluck('page_start', 'visitor_id');

        // ── Agent names for this site ──
        $agentNames = User::where('site_id', $user->site_id)
            ->pluck('name', 'id');

        $mapped = $visitors->map(function ($v) use ($totalTimes, $agentNames, $currentPageStarts) {
            // ── Total time from DB ──
            $totalTime = (int) ($totalTimes[$v->visitor_id] ?? 0);

            // ── Add live time for current page if visitor is online ──
            if ($v->status !== 'left_website') {
                $pageStart = $currentPageStarts[$v->visitor_id] ?? null;
                if ($pageStart) {
                    $secondsOnCurrentPage = Carbon::now()->diffInSeconds(Carbon::parse($pageStart));
                    // Cap at 2 hours to prevent runaway timers
                    if ($secondsOnCurrentPage < 7200) {
                        $totalTime += max(0, $secondsOnCurrentPage);
                    }
                } else if ($v->last_seen_at) {
                    // Fallback if no page history exists
                    $secondsSinceLastSeen = Carbon::now()->diffInSeconds(Carbon::parse($v->last_seen_at));
                    if ($secondsSinceLastSeen < 120) {
                        $totalTime += max(0, $secondsSinceLastSeen);
                    }
                }
            }

            // ── Agent assignment ──
            $assignUserName = null;
            if ($v->assign_userID) {
                $assignUserName = $agentNames[(int) $v->assign_userID] ?? null;
            }

            return [
                'visitor_id'      => $v->visitor_id,
                'domain_id'       => $v->domain_id,
                'name'            => $v->name ?? 'Unnamed Customer',
                'email'           => $v->email ?? '-',
                'ip_address'      => $v->ip_address,
                'status'          => $v->status,
                'country'         => $v->country,
                'state'           => $v->state,
                'city'            => $v->city,
                'lat'             => $v->lat,
                'lon'             => $v->lon,
                'countryCode'     => $v->countryCode ? strtolower($v->countryCode) : null,
                'device_type'     => $v->device_type,
                'os'              => $v->os,
                'browser'         => $v->browser,
                'last_page_url'   => $v->last_page_url,
                'last_page_title' => $v->last_page_title,
                'referrer_url'    => $v->referrer_url,
                'visit_count'     => $v->visit_count,
                'chat_count'      => $v->chat_count,
                'total_time'      => $totalTime,
                'last_seen_at'    => $v->last_seen_at?->toIso8601String(),
                'assign_userID'   => $v->assign_userID,
                'assign_userName' => $assignUserName,
            ];
        });

        return response()->json([
            'visitors' => $mapped,
            'count'    => $mapped->count(),
        ]);
    }

    /**
     * Single visitor details + page history
     */
    public function show($visitorId)
    {
        $visitor = Visitor::where('visitor_id', $visitorId)
            ->where('site_id', Auth::user()->site_id)
            ->firstOrFail();

        $pages = VisitorPage::where('visitor_id', $visitorId)
            ->orderBy('visited_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($p) {
                return [
                    'url'        => $p->url,
                    'title'      => $p->title ?? parse_url($p->url, PHP_URL_PATH),
                    'time_spent' => $p->time_spent,
                    'visited_at' => $p->visited_at
                        ? Carbon::parse($p->visited_at)->diffForHumans()
                        : null,
                ];
            });

        $totalTime = VisitorPage::where('visitor_id', $visitorId)->sum('time_spent');

        // ── Agent name for this visitor ──
        $assignUserName = null;
        if ($visitor->assign_userID) {
            $assignUserName = User::where('id', (int) $visitor->assign_userID)->value('name');
        }

        return response()->json([
            'visitor' => [
                'visitor_id'      => $visitor->visitor_id,
                'name'            => $visitor->name,
                'email'           => $visitor->email,
                'ip_address'      => $visitor->ip_address,
                'status'          => $visitor->status,
                'country'         => $visitor->country,
                'state'           => $visitor->state,
                'city'            => $visitor->city,
                'lat'             => $visitor->lat,
                'lon'             => $visitor->lon,
                'country_code'    => $visitor->countryCode,
                'device_type'     => $visitor->device_type,
                'browser'         => $visitor->browser,
                'os'              => $visitor->os,
                'last_page_url'   => $visitor->last_page_url,
                'referrer_url'    => $visitor->referrer_url,
                'visit_count'     => $visitor->visit_count,
                'chat_count'      => $visitor->chat_count,
                'last_seen_at'    => $visitor->last_seen_at?->toIso8601String(),
                'assign_userID'   => $visitor->assign_userID,
                'assign_userName' => $assignUserName,
            ],
            'pages'      => $pages,
            'total_time' => (int) $totalTime,
        ]);
    }
}