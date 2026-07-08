<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendWorkspaceLinksNotification;

class WorkspaceController extends Controller
{
    // Show the form to request workspace links
    public function showRequestForm()
    {
        return view('auth.forgot-workspace');
    }

    // Process the email and send notifications
    public function sendWorkspaceLinks(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->tenants->count() > 0) {
            // Send email with all tenant links attached
            $user->notify(new SendWorkspaceLinksNotification($user->tenants));
        }

        // Always return the same message to prevent email enumeration
        return back()->with('status', 'If an account is associated with that email, we have sent your workspace links.');
    }
}