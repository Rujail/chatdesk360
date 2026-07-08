<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        // Pass tenant to view if resolved by middleware
        return view('auth.login', [
            'tenant' => tenant() // Using the helper we created earlier
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Authenticate the user (checks email/password)
        $request->authenticate();

        // 2. ✅ Check if the user is suspended
        if (Auth::user() && Auth::user()->is_suspended) {
            // Log them out immediately
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Send them back to login with an error message
            throw ValidationException::withMessages([
                'email' => 'Your account has been suspended. Please contact your administrator.',
            ]);
        }

        // 3. If not suspended, regenerate session and proceed
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}