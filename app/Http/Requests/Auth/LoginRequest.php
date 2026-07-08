<?php

namespace App\Http\Requests\Auth;

use App\Models\Tenant;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $host = $this->getHost();
        $mainDomain = config('app.domain');

        // 1. If on the main domain, STOP login attempt
        if ($host === $mainDomain || $host === 'www.' . $mainDomain) {
            throw ValidationException::withMessages([
                'email' => 'Please log in through your workspace URL (e.g., yourworkspace.abc.com).',
            ]);
        }

        // 2. Find tenant by current subdomain
        $tenant = \App\Models\Tenant::where('domain_name', $host)->first();

        if (!$tenant) {
            throw ValidationException::withMessages(['email' => 'This workspace does not exist.']);
        }

        // 3. Find user by email + check if they belong to this tenant
        $user = \App\Models\User::where('email', $this->email)
            ->whereHas('tenants', function ($query) use ($tenant) {
                $query->where('tenants.id', $tenant->id);
            })
            ->first();

        // 4. Check password and login
        if (! $user || ! \Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}