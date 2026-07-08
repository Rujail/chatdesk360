<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WidgetSetting;
use App\Models\PostChatForm;
use App\Models\PostChatResponse;
use App\Models\ChatFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ChatTranscriptMail; 

class WidgetApiController extends Controller
{
    /* ── Default settings (match customizer.js) ─────── */
    const DEFAULTS = [
        'minimized_style'      => 'bubble',
        'theme'                => 'light',
        'primary_color'        => '#2b60d0',
        'use_custom_colors'    => false,
        'widget_bg_color'      => '#f7f7f7',
        'widget_text_color'    => '#1f2937',
        'position'             => 'right',
        'side_spacing'         => 24,
        'bottom_spacing'       => 24,
        'show_logo'            => false,
        'logo_url'             => '',
        'show_agent_photo'     => true,
        'sound_notifications'  => true,
        'allow_rating'         => true,
        'allow_transcripts'    => true,
        'white_label'          => false,
        'eye_catcher_image'    => '',
        'welcome_header'       => 'Welcome!',
        'welcome_title'        => 'Text us',
        'admin_name'           => 'Admin',
        'welcome_message'      => 'Hello. How may I help you?',
    ];

    /**
     * POST /api/widget/settings  { site_id }
     * Returns merged default + saved settings for the site.
     */
    public function getSettings(Request $request)
    {
        $request->validate(['site_id' => 'required|string']);

        $row = WidgetSetting::where('site_id', $request->site_id)->first();

        // ★ Ensure saved is always an array, never a string
        $saved = [];

        if ($row && $row->settings) {
            if (is_array($row->settings)) {
                $saved = $row->settings;
            } elseif (is_string($row->settings)) {
                // Fallback if model cast didn't work
                $decoded = json_decode($row->settings, true);
                $saved = is_array($decoded) ? $decoded : [];
            }
        }

        return response()->json([
            'success'  => true,
            'settings' => array_merge(self::DEFAULTS, $saved),
        ]);
    }

    /**
     * POST /api/widget/post-chat-config  { site_id }
     * Returns the post-chat form config (enabled flag + fields).
     */
    public function getPostChatConfig(Request $request)
    {
        $request->validate(['site_id' => 'required|string']);

        $row = PostChatForm::where('site_id', $request->site_id)->first();

        if (!$row || !$row->enabled) {
            return response()->json([
                'success' => true,
                'enabled' => false,
                'fields'  => [],
            ]);
        }

        // ★ Ensure form_config is always an array
        $fields = [];
        if ($row->form_config) {
            if (is_array($row->form_config)) {
                $fields = $row->form_config;
            } elseif (is_string($row->form_config)) {
                $decoded = json_decode($row->form_config, true);
                $fields = is_array($decoded) ? $decoded : [];
            }
        }

        return response()->json([
            'success' => true,
            'enabled' => true,
            'fields'  => $fields,
        ]);
    }

    /**
     * POST /api/widget/post-chat-response
     * Saves visitor's post-chat form response.
     */
    public function savePostChatResponse(Request $request)
    {
        $request->validate([
            'site_id'       => 'required|string',
            'visitor_id'    => 'required|string',
            'response_data' => 'required|array',
        ]);

        PostChatResponse::create([
            'site_id'       => $request->site_id,
            'visitor_id'    => $request->visitor_id,
            'response_data' => $request->response_data,
        ]);

        return response()->json(['success' => true, 'message' => 'Response saved.']);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'site_id'    => 'required|string|max:64',
            'visitor_id' => 'required|string|max:64',
            'file'       => 'required|file|max:10240', // 10MB max
        ]);

        // Validate site
        $user = User::where('site_id', $request->site_id)->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid site ID'], 403);
        }

        $file = $request->file('file');
        $mime = $file->getMimeType();
        $isImage = str_starts_with($mime, 'image/');
        $fileType = $isImage ? 'image' : 'file';

        // Store file
        $path = $file->store("chat-files/{$request->site_id}", 'public');
        $url = url('/api/chat-files/' . $request->site_id . '/' . basename($path));

        // Save to database
        $chatFile = ChatFile::create([
            'site_id'      => $request->site_id,
            'visitor_id'   => $request->visitor_id,
            'original_name'=> $file->getClientOriginalName(),
            'file_path'    => $path,
            'file_url'     => $url,
            'mime_type'    => $mime,
            'file_size'    => $file->getSize(),
            'file_type'    => $fileType,
        ]);

        return response()->json([
            'success'  => true,
            'file_id'  => $chatFile->id,
            'file_url' => $url,
            'file_type'=> $fileType,
            'file_name'=> $file->getClientOriginalName(),
        ]);
    }
    public function sendTranscript(Request $request)
    {
        $request->validate([
            'site_id'     => 'required|string',
            'visitor_id'  => 'required|string',
            'email'       => 'required|email',
            'domain_id'   => 'required|string', // e.g., 'autofortrade_co_uk'
        ]);

        $siteId    = $request->site_id;
        $visitorId = $request->visitor_id;
        $email     = $request->email;
        $domainId  = $request->domain_id; 

        // Pass all three variables to the Mailable
        Mail::to($email)->send(new ChatTranscriptMail($siteId, $visitorId, $domainId));

        return response()->json(['success' => true, 'message' => 'Transcript email is being sent.']);
    }
}