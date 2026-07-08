<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\PostChatResponse;
use Illuminate\Http\Request;

class PostChatResponseController extends Controller
{
    public function index()
    {
        $siteId = auth()->user()->site_id;

        $responses = PostChatResponse::where('site_id', $siteId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('settings.post-chat-responses.index', compact('responses'));
    }

    public function show($id)
    {
        $siteId = auth()->user()->site_id;

        $response = PostChatResponse::where('site_id', $siteId)
            ->where('id', $id)
            ->firstOrFail();

        return view('settings.post-chat-responses.show', compact('response'));
    }
}