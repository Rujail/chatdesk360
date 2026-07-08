<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return view('dashboard', ['message' => 'Welcome, Admin! You have full access.']);
        } elseif ($user->isAgent()) {
            return view('dashboard', ['message' => 'Welcome, Agent! Limited access here.']);
        }
        return view('dashboard', ['message' => 'Welcome, User! Basic access.']);
    }
}