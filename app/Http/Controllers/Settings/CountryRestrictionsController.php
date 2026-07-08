<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\CountryRestriction;
use Illuminate\Http\Request;

class CountryRestrictionsController extends Controller
{
    public function index()
    {
        return view('settings.country-restrictions.index');
    }

    public function list()
    {
        $restricted = CountryRestriction::where('site_id', auth()->user()->site_id)->get();
        return response()->json(['restricted' => $restricted]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|size:2',
            'country_name' => 'required|string',
            'is_restricted' => 'required|boolean',
        ]);

        $siteId = auth()->user()->site_id;

        if ($request->is_restricted) {
            CountryRestriction::firstOrCreate(
                ['site_id' => $siteId, 'country_code' => $request->country_code],
                ['country_name' => $request->country_name]
            );
        } else {
            CountryRestriction::where('site_id', $siteId)
                ->where('country_code', $request->country_code)
                ->delete();
        }

        return response()->json(['success' => true]);
    }
}