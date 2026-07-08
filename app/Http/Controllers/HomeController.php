<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Dynamic Greeting based on time of day
        $hour = now()->format('H');
        if ($hour < 12) {
            $greeting = 'Good Morning';
        } elseif ($hour < 17) {
            $greeting = 'Good Afternoon';
        } else {
            $greeting = 'Good Evening';
        }

        return view('dashboard.index', compact('greeting'));
    }

    /**
     * Get live stats for the dashboard cards
     */
    public function stats()
    {
        $siteId = Auth::user()->site_id;
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);

        // Customers online (seen in last 5 minutes and not left)
        $onlineCustomers = Visitor::where('site_id', $siteId)
            ->where('last_seen_at', '>=', $fiveMinutesAgo)
            ->where('status', '!=', 'left_website')
            ->count();

        // Ongoing chats
        $ongoingChats = Visitor::where('site_id', $siteId)
            ->where('status', 'chatting')
            ->count();

        // ★ Active agents on this site (Not offline AND seen in the last 5 minutes)
        $activeAgents = User::where('site_id', $siteId)
            ->where('status', '!=', 'offline')
            ->where('last_seen_at', '>=', $fiveMinutesAgo)
            ->count();

        return response()->json([
            'online_customers' => $onlineCustomers,
            'ongoing_chats' => $ongoingChats,
            'active_agents' => $activeAgents,
        ]);
    }

    /**
     * Get chart data for the selected date range
     */
    public function chart(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date',
        ]);

        $siteId = Auth::user()->site_id;
        $startDate = Carbon::parse($request->start)->startOfDay();
        $endDate = Carbon::parse($request->end)->endOfDay();

        // Fetch visitors who chatted in the date range
        $chats = Visitor::where('site_id', $siteId)
            ->where('chat_count', '>', 0)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->keyBy('date');

        // Fill missing dates with 0
        $period = Carbon::parse($startDate)->daysUntil($endDate);
        $data = [];

        foreach ($period as $day) {
            $dateString = $day->format('Y-m-d');
            $data[] = [
                'date' => $dateString,
                'dateLabel' => $day->format('M d'),
                'total' => $chats->has($dateString) ? $chats[$dateString]->total : 0,
            ];
        }

        return response()->json($data);
    }
}