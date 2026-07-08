<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\VisitorPage;
use App\Models\Visitor;

class VisitorStatsController extends Controller
{
    public function getStats($visitorId)
    {
        $pages = VisitorPage::where('visitor_id', $visitorId)->get();

        return response()->json([
            'total_pages' => $pages->count(),
            'total_time'  => $pages->sum('time_spent'),
        ]);
    }
}
