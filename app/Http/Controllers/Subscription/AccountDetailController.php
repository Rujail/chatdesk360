<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;

class AccountDetailController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Stripe se user ka default payment method fetch karo
        $paymentMethod = null;
        if ($user->hasDefaultPaymentMethod()) {
            $paymentMethod = $user->defaultPaymentMethod();
        }

        $subscription = $user->subscription('default');
        
        // Company Details aur Agents (Owner change ke liye)
        $site = $user->site;
        $agents = User::where('site_id', $user->site_id)->where('role', '!=', 'admin')->get();

        return view('subscription.account-details.index', compact('paymentMethod', 'subscription', 'site', 'agents'));
    }

    public function getSetupIntent()
    {
        return response()->json(['clientSecret' => auth()->user()->createSetupIntent()->client_secret]);
    }

    public function updatePaymentMethod(Request $request)
    {
        $request->validate(['payment_method_id' => 'required|string']);
        $user = auth()->user();

        try {
            // Naya card add karo aur default banao
            $user->updateDefaultPaymentMethod($request->payment_method_id);
            
            // 🔹 Purane cards ko delete kar do taake sirf 1 card save ho
            $user->deletePaymentMethods(); 

            return response()->json(['success' => true, 'message' => 'Payment method updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancelSubscription(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->subscription('default');

        if ($subscription && $subscription->active()) {
            $subscription->cancel(); // Cancel at period end
            return redirect()->back()->with('success', 'Subscription canceled. Active until period ends.');
        }

        return redirect()->back()->with('error', 'No active subscription found.');
    }

    // 🔹 Naya: Company Details Update
    public function updateCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'company_size' => 'required|string|max:255',
        ]);

        $site = Site::where('site_id', auth()->user()->site_id)->first();
        if ($site) {
            $site->update([
                'name' => $request->name,
                'country' => $request->country,
                'company_size' => $request->company_size,
            ]);
        }

        return redirect()->back()->with('success', 'Company details updated successfully.');
    }

    // 🔹 Naya: Account Owner Change
    public function changeOwner(Request $request)
    {
        $request->validate([
            'new_owner_id' => 'required|exists:users,id',
        ]);

        $currentOwner = auth()->user();
        $newOwner = User::findOrFail($request->new_owner_id);

        // Ensure new owner belongs to same site
        if ($currentOwner->site_id !== $newOwner->site_id) {
            return redirect()->back()->with('error', 'User does not belong to this workspace.');
        }

        $site = Site::where('site_id', $currentOwner->site_id)->first();
        if ($site) {
            // Swap roles
            $currentOwner->update(['role' => 'agent']);
            $newOwner->update(['role' => 'admin']);
            
            // Update site owner
            $site->update(['owner_id' => $newOwner->id]);
        }

        return redirect()->back()->with('success', 'Workspace ownership transferred successfully.');
    }
}