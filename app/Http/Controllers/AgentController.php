<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Site;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use App\Notifications\InviteUserNotification;
use App\Notifications\WelcomeAgentNotification;

class AgentController extends Controller
{
    public function index() {
        $users = User::where('site_id', auth()->user()->site_id)->get();
        return view('agents.index', compact('users'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            // Removed 'password' validation
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $admin = auth()->user();

        if (empty($admin->site_id)) {
            return response()->json([
                'status' => false, 
                'errors' => ['site' => 'Your admin account does not have a valid workspace (site_id).']
            ], 200);
        }

        $site = Site::where('site_id', $admin->site_id)->first();

        if (!$site) {
            return response()->json([
                'status' => false, 
                'errors' => ['site' => 'Site configuration not found for your account.']
            ], 200);
        }

        $currentAgentsCount = User::where('site_id', $site->site_id)->where('role', 'agent')->count();
        $pendingInvitesCount = Invitation::where('site_id', $site->site_id)->where('role', 'agent')->count();
        $availableSlots = $site->agent_limit - ($currentAgentsCount + $pendingInvitesCount);

        if ($availableSlots <= 0) {
            return response()->json([
                'status' => false, 
                'errors' => ['limit' => 'You have reached your maximum agent limit of ' . $site->agent_limit . '. Please upgrade your subscription or cancel pending invites.']
            ], 200);
        }

        // ✅ Generate a random secure password in the background
        $randomPassword = Str::random(16);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($randomPassword), // Save the random password
            'role' => 'agent',
            'site_id' => $admin->site_id,
            'status' => 'accepting_chats',
            'concurrent_chat_limit' => 6,
        ]);

        $tenant = Tenant::where('site_id', $admin->site_id)->first();
        if ($tenant) {
            $tenant->users()->attach($user->id);
        }

        // ✅ Generate a Password Reset Token for the new user
        $token = Password::createToken($user);

        // ✅ Send Welcome Email with Reset Link
        $user->notify(new WelcomeAgentNotification($token, $site->name));

        return response()->json(['status' => true, 'message' => 'Agent created successfully. A welcome email with a password setup link has been sent.']);
    }

    public function edit($id) {
        $agent = User::where('site_id', auth()->user()->site_id)->findOrFail($id);
        return view('agents.edit', compact('agent'));
    }

    public function update(Request $request, $id) {
        $agent = User::where('site_id', auth()->user()->site_id)->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'concurrent_chat_limit' => 'required|integer|min:1',
            'status' => 'required|in:accepting_chats,not_accepting_chats,offline',
        ]);

        $agent->update($request->only(['name', 'email', 'concurrent_chat_limit', 'status', 'groups']));

        return redirect()->route('agents.edit', $agent->id)->with('success', 'Agent updated successfully');
    }

    public function invite(Request $request) {
        $emails = $request->emails;
        $roles = $request->roles;
        $adminSiteId = auth()->user()->site_id;

        if (empty($adminSiteId)) {
            return response()->json(['status' => false, 'errors' => ['site' => 'Your admin account does not have a valid workspace.']], 200); // Changed to 200
        }

        $site = Site::where('site_id', $adminSiteId)->first();

        // ✅ Count ONLY agents, excluding the admin
        $currentAgentsCount = User::where('site_id', $adminSiteId)->where('role', 'agent')->count();
        $pendingInvitesCount = Invitation::where('site_id', $adminSiteId)->where('role', 'agent')->count();
        
        // Filter out empty emails and count only agent invites
        $validEmails = [];
        $newInvitesCount = 0;
        foreach ($emails as $index => $email) {
            if (!empty($email)) {
                $validEmails[$index] = $email;
                if (($roles[$index] ?? 'agent') === 'agent') {
                    $newInvitesCount++;
                }
            }
        }

        $availableSlots = $site->agent_limit - ($currentAgentsCount + $pendingInvitesCount);

        if ($newInvitesCount > $availableSlots) {
            return response()->json([
                'status' => false, 
                'errors' => ['limit' => 'You only have ' . $availableSlots . ' available slot(s) left, but you are trying to invite ' . $newInvitesCount . ' agent(s). Please upgrade your subscription.']
            ], 200); // Changed to 200
        }

        try {
            foreach ($validEmails as $index => $email) {
                $token = Str::random(40);
                Invitation::create([
                    'email' => $email,
                    'role' => $roles[$index] ?? 'agent',
                    'site_id' => $adminSiteId, 
                    'token' => $token,
                    'expires_at' => now()->addDays(3),
                ]);
                
                Notification::route('mail', $email)->notify(new InviteUserNotification($token));
            }
            
            return response()->json(['status' => true, 'message' => 'Invitations sent successfully!']);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => ['error' => $e->getMessage()]], 500);
        }
    }

    public function acceptInvite($token) {
        $invite = Invitation::where('token', $token)->firstOrFail();
        return view('auth.accept-invite', compact('invite'));
    }

    public function completeInvite(Request $request) {
        $request->validate(['token' => 'required', 'password' => 'required|min:6']);
        $invite = Invitation::where('token', $request->token)->firstOrFail();

        $siteId = $invite->site_id;
        if (empty($siteId)) {
            $site = Site::where('owner_id', $invite->user_id ?? auth()->id())->first();
            $siteId = $site ? $site->site_id : null;
        }

        $user = User::create([
            'name' => explode('@', $invite->email)[0],
            'email' => $invite->email,
            'password' => Hash::make($request->password),
            'role' => $invite->role,
            'site_id' => $siteId,
            'status' => 'not_accepting_chats',
        ]);

        if ($siteId) {
            $tenant = Tenant::where('site_id', $siteId)->first();
            if ($tenant) {
                $tenant->users()->attach($user->id);
            }
        }

        $invite->delete();
        return redirect('/login')->with('success', 'Account created successfully');
    }

    /**
     * Fetch agents for the transfer dropdown.
     */
    public function listAgents()
    {
        $siteId = auth()->user()->site_id;
        $currentAgentId = auth()->id();

        $agents = User::where('site_id', $siteId)
            ->where('id', '!=', $currentAgentId)
            ->get()
            ->map(function($user) {
                $isOnline = $user->last_seen_at && $user->last_seen_at->gt(\Carbon\Carbon::now()->subMinutes(2));
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_online' => $isOnline,
                    'last_seen' => $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never'
                ];
            });

        return response()->json(['agents' => $agents]);
    }

        /**
     * Update agent status (Accepting, Not Accepting, Offline)
     */
    public function updateStatus(Request $request, $id)
    {
        $agent = User::where('site_id', auth()->user()->site_id)->findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:accepting_chats,not_accepting_chats,offline'
        ]);

        $agent->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => 'Status updated successfully']);
    }

    /**
     * Update concurrent chat limit
     */
    public function updateChatLimit(Request $request, $id)
    {
        $agent = User::where('site_id', auth()->user()->site_id)->findOrFail($id);
        
        $request->validate([
            'concurrent_chat_limit' => 'required|integer|min:1'
        ]);

        $agent->update(['concurrent_chat_limit' => $request->concurrent_chat_limit]);

        return response()->json(['status' => true, 'message' => 'Chat limit updated successfully']);
    }

    /**
     * Delete an agent
     */
    public function destroy($id)
    {
        $agent = User::where('site_id', auth()->user()->site_id)->findOrFail($id);

        // Prevent admin from deleting themselves
        if ($agent->id === auth()->id()) {
            return response()->json(['status' => false, 'errors' => ['error' => 'You cannot delete your own account.']], 200);
        }

        $agent->delete();

        return response()->json(['status' => true, 'message' => 'Agent deleted successfully']);
    }

        /**
     * Suspend an agent
     */
    public function suspend($id)
    {
        $agent = User::where('site_id', auth()->user()->site_id)->findOrFail($id);
        
        if ($agent->id === auth()->id()) {
            return response()->json(['status' => false, 'errors' => ['error' => 'You cannot suspend your own account.']], 200);
        }

        $agent->update([
            'is_suspended' => true,
            'status' => 'offline'
        ]);

        return response()->json(['status' => true, 'message' => 'Agent suspended successfully. They will be logged out.']);
    }

    /**
     * Activate/Unsuspend an agent
     */
    public function activate($id)
    {
        $agent = User::where('site_id', auth()->user()->site_id)->findOrFail($id);
        
        $agent->update([
            'is_suspended' => false,
            'status' => 'accepting_chats'
        ]);

        return response()->json(['status' => true, 'message' => 'Agent activated successfully.']);
    }

    /**
     * Fetch agent details for offcanvas via AJAX.
     */
    public function show($id)
    {
        $agent = User::where('site_id', auth()->user()->site_id)->findOrFail($id);

        return response()->json([
            'id' => $agent->id,
            'name' => $agent->name,
            'email' => $agent->email,
            'role' => $agent->role,
            'status' => $agent->status,
            'chat_limit' => $agent->concurrent_chat_limit,
            'groups' => $agent->groups ?? 'N/A',
            'total_chats' => $agent->total_chats_handled ?? 0,
            'satisfaction' => $agent->avg_satisfaction ?? 'N/A',
            'last_seen' => $agent->last_seen_at ? $agent->last_seen_at->diffForHumans() : 'Never'
        ]);
    }
}