<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\PostChatForm;
use Illuminate\Http\Request;

class PostChatFormController extends Controller
{
    public function index()
    {
        $siteId = auth()->user()->site_id;

        $postChatForm = PostChatForm::where('site_id', $siteId)->first();

        return view('settings.post-chat-form.index', [
            'siteId'       => $siteId,
            'postChatForm' => $postChatForm,
        ]);
    }

    /**
     * POST /settings/post-chat-form/save
     */
    public function save(Request $request)
    {
        $siteId = auth()->user()->site_id;

        $request->validate([
            'enabled'     => 'required|boolean',
            'form_config' => 'nullable|array',
        ]);

        PostChatForm::updateOrCreate(
            ['site_id' => $siteId],
            [
                'enabled'     => $request->enabled,
                'form_config' => $request->form_config ?? [],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Post-chat form saved!',
        ]);
    }
}