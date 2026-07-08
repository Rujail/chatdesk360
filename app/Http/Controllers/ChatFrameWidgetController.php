<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatFrameWidgetController extends Controller
{
    public function iframe(Request $request)
    {
        $siteId = $request->query('site_id');
        
        if (!$siteId) {
            abort(403, 'Site ID missing');
        }

        // TODO: Validate site_id exists in your database
        // $site = Site::where('site_id', $siteId)->firstOrFail();

        // Pass data to the Blade view
        return view('widget.iframe', [
            'siteId' => $siteId
        ]);
    }
}