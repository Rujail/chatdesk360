<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Import your User model
use Kreait\Firebase\Factory;

class ChatAuthenticationController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'site_id'    => 'required|string',
            'visitor_id' => 'required|string',
        ]);

        $siteId = $request->site_id;
        $visitorId = $request->visitor_id;

        // 1. Check if any user exists with this site_id
        // This verifies the site is "active" in your system
        $siteValid = User::where('site_id', $siteId)->exists();

        if (!$siteValid) {
            return response()->json(['error' => 'Invalid Site ID'], 403);
        }

        try {
            $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-auth.json'));
            $auth = $factory->createAuth();

            // 1. Create the CustomToken object
           $customTokenObject = $auth->createCustomToken($visitorId, [
                'site_id' => $siteId
            ]);


            /**
             * 2. FIX: The error "Object of class ... could not be converted to string"
             * happens because we can't use (string) casting anymore.
             * We must call the method that returns the actual JWT string.
             */
            $jwtString = $customTokenObject->toString();

            // 3. Return the actual string in the JSON response
            return response()->json([
                'token' => $jwtString, 
                'visitor_id' => $visitorId
            ]);
        } catch (\Exception $e) {
            // Log the error so you can see it in storage/logs/laravel.log
            \Log::error("Firebase Auth Error: " . $e->getMessage());
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}