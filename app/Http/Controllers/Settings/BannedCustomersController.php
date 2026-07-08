<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\BannedCustomer;
use Illuminate\Http\Request;

class BannedCustomersController extends Controller
{
    /* ── Settings page ────────────────────────────────────── */
    public function index()
    {
        return view('settings.banned-customers.index');
    }

    /* ── List banned customers (AJAX) ─────────────────────── */
    public function list(Request $request)
    {
        $bans = BannedCustomer::with('bannedBy')
            ->where('site_id', auth()->user()->site_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ban) {
                return [
                    'id'           => $ban->id,
                    'ip_address'   => $ban->ip_address,
                    'visitor_id'   => $ban->visitor_id,
                    'chat_id'      => $ban->chat_id,
                    'reason'       => $ban->reason,
                    'banned_by'    => $ban->bannedBy?->name ?? '-',
                    'start_date'   => $ban->start_date?->format('d M Y'),
                    'end_date'     => $ban->is_permanent ? 'Permanent' : $ban->end_date?->format('d M Y'),
                    'is_permanent' => $ban->is_permanent,
                    'is_active'    => $ban->isActive(),
                    'created_at'   => $ban->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json(['bans' => $bans]);
    }

    /* ── Ban a customer ───────────────────────────────────── */
    public function store(Request $request)
    {
        $data = $request->validate([
            'ip_address' => 'nullable|string|max:45',
            'visitor_id' => 'nullable|string|max:100',
            'chat_id'    => 'nullable|string|max:50',
            'reason'     => 'nullable|string|max:500',
            'duration'   => 'required|in:1,7,30,permanent,custom',
            'custom_end' => 'nullable|date|after:now',
        ]);

        $siteId   = auth()->user()->site_id;   // string like 'site_ZXTth2HlCT'
        $duration = $data['duration'];

        $endDate   = null;
        $permanent = false;

        if ($duration === 'permanent') {
            $permanent = true;
        } elseif ($duration === 'custom') {
            $endDate = $data['custom_end'] ?? null;
        } else {
            $endDate = now()->addDays((int) $duration);
        }

        // Check for duplicate active ban
        $existing = BannedCustomer::activeForSite($siteId)
            ->where(function ($q) use ($data) {
                if ($data['ip_address']) $q->orWhere('ip_address', $data['ip_address']);
                if ($data['visitor_id']) $q->orWhere('visitor_id', $data['visitor_id']);
            })
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'This customer is already banned.',
            ], 409);
        }

        $ban = BannedCustomer::create([
            'site_id'      => $siteId,
            'ip_address'   => $data['ip_address'] ?? null,
            'visitor_id'   => $data['visitor_id'] ?? null,
            'chat_id'      => $data['chat_id'] ?? null,
            'reason'       => $data['reason'] ?? null,
            'banned_by'    => auth()->id(),
            'start_date'   => now(),
            'end_date'     => $endDate,
            'is_permanent' => $permanent,
        ]);

        return response()->json([
            'message' => 'Customer banned successfully.',
            'ban'     => $ban,
        ], 201);
    }

    /* ── Unban a customer ─────────────────────────────────── */
    public function destroy($id)
    {
        $ban = BannedCustomer::where('site_id', auth()->user()->site_id)
            ->findOrFail($id);

        $ban->delete();

        return response()->json([
            'message' => 'Customer unbanned successfully.',
        ]);
    }

    /* ── API: Check if visitor is banned (called by widget) ─ */
    public function checkBan(Request $request)
    {
        $request->validate([
            'visitor_id' => 'nullable|string',
            'site_id'    => 'required|string',      // ← STRING
        ]);

        $siteId    = $request->input('site_id');
        $visitorId = $request->input('visitor_id');
        $ip        = $request->ip();

        $banned = BannedCustomer::activeForSite($siteId)
            ->where(function ($q) use ($visitorId, $ip) {
                if ($visitorId) $q->orWhere('visitor_id', $visitorId);
                if ($ip)        $q->orWhere('ip_address', $ip);
            })
            ->exists();

        return response()->json([
            'banned' => $banned,
        ]);
    }
}