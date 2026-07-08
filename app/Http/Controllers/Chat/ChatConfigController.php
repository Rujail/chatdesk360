<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrustedDomain;
use App\Models\DetectedDomain;
use App\Models\User;
use App\Models\CountryRestriction;
use App\Models\Visitor;
use Illuminate\Support\Facades\Http;

class ChatConfigController extends Controller
{
    public function getConfig(Request $request)
    {
        $request->validate([
            'site_id'    => 'required|string|max:64',
            'parent_url' => 'nullable|string|max:500',
        ]);

        $user = User::where('site_id', $request->site_id)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid site ID.'], 403);
        }

        $parentUrl = $request->input('parent_url', '');

        if ($parentUrl) {
            // ★ parent_url provided — extract domain from it
            if (!str_starts_with($parentUrl, 'http')) {
                $parentUrl = 'https://' . $parentUrl;
            }
            $parsedHost    = parse_url($parentUrl, PHP_URL_HOST) ?? '';
            $requestDomain = TrustedDomain::normalizeDomain($parsedHost);
        } else {
            // ★ No parent_url — fall back to Origin/Referer headers
            $origin = $request->header('Origin') ?? $request->header('Referer') ?? '';
            if (!$origin) {
                return response()->json(['error' => 'Missing origin'], 403);
            }
            if (!str_starts_with($origin, 'http')) {
                $origin = 'https://' . $origin;
            }
            $parsedHost    = parse_url($origin, PHP_URL_HOST) ?? '';
            $requestDomain = TrustedDomain::normalizeDomain($parsedHost);
        }

        $isTrusted = TrustedDomain::isTrusted($user->site_id, $requestDomain);

        $isLocalhost = in_array($requestDomain, ['localhost', '127.0.0.1', '::1'])
            || str_ends_with($requestDomain, '.test')
            || str_ends_with($requestDomain, '.local');

        if (!$isTrusted && !$isLocalhost) {

            DetectedDomain::logAttempt(
                $user->site_id,
                $user->id,
                $requestDomain,
                $request->ip()
            );

            return response()->json([
                'error' => 'Unauthorized domain'
            ], 403);
        }

        // ★★★ COUNTRY RESTRICTION CHECK ★★★
        $ip = $request->ip();

        // Bypass for local development
        if ($ip !== '127.0.0.1' && $ip !== '::1') {
            $countryCode = null;

            // 1. Check if visitor already has country resolved in DB
            $visitor = Visitor::where('ip_address', $ip)
                ->where('site_id', $request->site_id)
                ->latest()
                ->first();

            if ($visitor && $visitor->countryCode) {
                $countryCode = $visitor->countryCode;
            } else {
                // 2. Fallback: Resolve via API if not in DB
                try {
                    $response = Http::get("http://ip-api.com/json/{$ip}?fields=countryCode");
                    if ($response->successful()) {
                        $countryCode = $response->json()['countryCode'] ?? null;
                    }
                } catch (\Exception $e) {}
            }

            // 3. If we successfully got a country code, check if it's restricted
            if ($countryCode) {
                $isRestricted = CountryRestriction::where('site_id', $request->site_id)
                    ->where('country_code', $countryCode)
                    ->exists();

                if ($isRestricted) {
                    return response()->json(['error' => 'Country restricted'], 403);
                }
            }
        }

        return response()->json([
            'apiKey'      => config('services.firebase.api_key'),
            'databaseURL' => config('services.firebase.db_url'),
            'projectId'   => config('services.firebase.project_id'),
            'appId'       => config('services.firebase.app_id'),
            'authDomain'  => config('services.firebase.project_id') . '.firebaseapp.com',
        ]);
    }
}