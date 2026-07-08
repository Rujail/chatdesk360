<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TrustedDomain; // ★ Add this import

class InstallChatController extends Controller
{
    public function index() {
        $user = Auth::user();
        $siteId = $user->site_id;
        $baseUrl = config('app.url'); // Or hardcode 'https://chatdesk360.com'

        // ★ Check if at least one trusted domain exists for this site_id
        $isInstalled = TrustedDomain::where('site_id', $siteId)->exists();

        return view('settings.chat-install.index', compact('siteId', 'baseUrl', 'isInstalled'));
    }
}