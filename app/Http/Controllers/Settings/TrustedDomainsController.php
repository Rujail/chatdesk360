<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrustedDomain;
use App\Models\DetectedDomain;
use Illuminate\Support\Facades\Auth;

class TrustedDomainsController extends Controller
{
    /**
     * Trusted + Detected domains page
     */
    public function index()
    {
        $siteId = Auth::user()->site_id;

        if (!$siteId) {
            return back()->withErrors(['error' => 'No site_id associated with your account.']);
        }

        $trustedDomains = TrustedDomain::where('site_id', $siteId)
            ->orderByDesc('created_at')
            ->get();

        $detectedDomains = DetectedDomain::where('site_id', $siteId)
            ->orderByDesc('last_attempt_at')
            ->get();

        $trustedCount   = $trustedDomains->count();
        $detectedCount  = $detectedDomains->count();

        return view('settings.trusted-domains.index', compact(
            'trustedDomains', 'detectedDomains', 'trustedCount', 'detectedCount'
        ));
    }

    /**
     * Domain add karo
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $siteId = Auth::user()->site_id;

        if (!$siteId) {
            return back()->withErrors(['domain' => 'No site_id found for your account.']);
        }

        $domain = TrustedDomain::normalizeDomain($request->domain);

        if (!preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,}$/', $domain)) {
            return back()
                ->withErrors(['domain' => 'Invalid domain format. Enter like: example.com'])
                ->withInput();
        }

        $exists = TrustedDomain::where('site_id', $siteId)
            ->where('domain', $domain)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['domain' => 'This domain is already trusted.'])
                ->withInput();
        }

        TrustedDomain::create([
            'user_id'   => Auth::id(),
            'site_id'   => $siteId,
            'domain'    => $domain,
            'added_by'  => Auth::user()->email,
        ]);

        // ★ Remove from detected_domains if it was there
        DetectedDomain::where('site_id', $siteId)
            ->where('domain', $domain)
            ->delete();

        return back()->with('success', "Domain '{$domain}' added successfully.");
    }

    /**
     * Domain delete karo
     */
    public function destroy($id)
    {
        $siteId = Auth::user()->site_id;

        $domain = TrustedDomain::where('id', $id)
            ->where('site_id', $siteId)
            ->firstOrFail();

        $domainName = $domain->domain;
        $domain->delete();

        return back()->with('success', "Domain '{$domainName}' removed.");
    }

    /**
     * Detected domain ko trusted mein move karo
     */
    public function trustDetected($id)
    {
        $siteId = Auth::user()->site_id;

        $detected = DetectedDomain::where('id', $id)
            ->where('site_id', $siteId)
            ->firstOrFail();

        // Check if already trusted
        $alreadyTrusted = TrustedDomain::where('site_id', $siteId)
            ->where('domain', $detected->domain)
            ->exists();

        if (!$alreadyTrusted) {
            TrustedDomain::create([
                'user_id'   => Auth::id(),
                'site_id'   => $siteId,
                'domain'    => $detected->domain,
                'added_by'  => Auth::user()->email,
            ]);
        }

        // Remove from detected
        $detected->delete();

        return back()->with('success', "Domain '{$detected->domain}' added to trusted.");
    }

    /**
     * Detected domain dismiss karo
     */
    public function dismissDetected($id)
    {
        $siteId = Auth::user()->site_id;

        $detected = DetectedDomain::where('id', $id)
            ->where('site_id', $siteId)
            ->firstOrFail();

        $domainName = $detected->domain;
        $detected->delete();

        return back()->with('success', "Detected domain '{$domainName}' dismissed.");
    }
}