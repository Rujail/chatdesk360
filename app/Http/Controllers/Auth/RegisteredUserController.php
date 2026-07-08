<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Package; // 👈 Add this
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $mainDomain = config('app.domain'); // abc.com

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'subdomain' => [
                'required', 'string', 'min:3', 'max:50', 
                'regex:/^[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9]$/',
                function ($attribute, $value, $fail) use ($mainDomain) {
                    $fullDomain = $value . '.' . $mainDomain;
                    if (Tenant::where('domain_name', $fullDomain)->exists()) {
                        $fail('This subdomain is already taken.');
                    }
                    $reserved = ['www', 'mail', 'ftp', 'admin', 'api', 'app'];
                    if (in_array(strtolower($value), $reserved)) {
                        $fail('This subdomain is reserved.');
                    }
                },
            ],
            'package' => ['nullable', 'string'], 
        ]);

        // Generate unique string site_id
        do {
            $siteId = 'site_' . Str::random(10);
        } while (Tenant::where('site_id', $siteId)->exists());

        // Create User
        $user = User::create([
            'site_id' => $siteId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'status' => 'offline',
        ]);

        // Create Tenant
        $fullDomain = $request->subdomain . '.' . $mainDomain;
        $tenant = Tenant::create([
            'site_id'     => $siteId,
            'user_id'     => $user->id,
            'domain_name' => $fullDomain,
        ]);
        $tenant->users()->attach($user->id);

        event(new Registered($user));

        // 🔹 CHECK PACKAGE IN DATABASE
        $packageSlug = $request->get('package', 'starter');
        
        // Check if package exists by slug (or title if you prefer)
        $package = Package::where('slug', $packageSlug)->first();
        
        // Fallback to 'starter' if the provided package doesn't exist in DB
        if (!$package) {
            $package = Package::where('slug', 'starter')->first();
        }

        // Generate Auto-Login SIGNED URL FOR SUBDOMAIN
        // Pass the package ID instead of the slug so the checkout page can query it directly
        $targetPath = URL::temporarySignedRoute(
            'auto.login', 
            now()->addMinutes(5), 
            [
                'user_id' => $user->id, 
                'package' => $package ? $package->id : null
            ],
            false // false = relative URL to prevent domain mismatch
        );

        $protocol = $request->secure() ? 'https' : 'http';

        // Append the relative signed path to the subdomain
        return redirect($protocol . '://' . $fullDomain . $targetPath);
    }
}