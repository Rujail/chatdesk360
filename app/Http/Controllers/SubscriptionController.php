<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;

class SubscriptionController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        
        // 🔹 Check which package the user is currently subscribed to
        $currentPackageId = null;
        if (auth()->check() && auth()->user()->subscribed('default')) {
            $subscription = auth()->user()->subscription('default');
            
            // Match the stripe_price ID with the packages
            $currentPackage = Package::where('stripe_monthly_price_id', $subscription->stripe_price)
                ->orWhere('stripe_annual_price_id', $subscription->stripe_price)
                ->first();
                
            if ($currentPackage) {
                $currentPackageId = $currentPackage->id;
            }
        }

        return view('subscription.index', compact('packages', 'currentPackageId'));
    }
}