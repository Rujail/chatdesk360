<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\WidgetSetting;
use App\Models\PostChatForm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WidgetController extends Controller
{
    /* ── Allowed setting keys ── */
    const ALLOWED_KEYS = [
        'minimized_style',
        'theme',
        'primary_color',
        'primary_hover',
        'use_custom_colors',
        'widget_bg_color',
        'widget_text_color',
        'position',
        'side_spacing',
        'bottom_spacing',
        'show_logo',
        'logo_url',
        'show_agent_photo',
        'sound_notifications',
        'allow_rating',
        'allow_transcripts',
        'white_label',
        'eye_catcher_image',
        'welcome_header',
        'welcome_title',
        'admin_name',
        'welcome_message',
    ];

    public function index()
    {
        $siteId = auth()->user()->site_id;

        $row = WidgetSetting::where('site_id', $siteId)->first();
        $savedSettings = $this->decodeSettings($row);

        $postChatRow = PostChatForm::where('site_id', $siteId)->first();

        return view('settings.widget.index', [
            'siteId'        => $siteId,
            'savedSettings' => $savedSettings,
            'postChatForm'  => $postChatRow,
        ]);
    }

    /**
     * POST /settings/widget/save
     */
    public function save(Request $request)
    {
        $siteId = auth()->user()->site_id;

        $settings = $request->input('settings', []);

        if (is_string($settings)) {
            $decoded = json_decode($settings, true);
            $settings = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($settings)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid settings format.',
            ], 422);
        }

        $clean = [];
        foreach (self::ALLOWED_KEYS as $key) {
            if (array_key_exists($key, $settings)) {
                $clean[$key] = $settings[$key];
            }
        }

        foreach ($clean as $k => $v) {
            if (is_string($v) && strlen($v) > 500) {
                $clean[$k] = substr($v, 0, 500);
            }
        }

        WidgetSetting::updateOrCreate(
            ['site_id' => $siteId],
            ['settings' => $clean]
        );

        return response()->json([
            'success' => true,
            'message' => 'Widget settings saved successfully!',
        ]);
    }

    /**
     * POST /settings/widget/upload-logo
     * ★ Stores directly in public_html/assets/logos/
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,gif,svg,webp|max:2048',
        ], [
            'logo.required'   => 'Please select a file to upload.',
            'logo.image'      => 'The file must be an image.',
            'logo.mimes'      => 'Allowed image types: JPEG, PNG, GIF, SVG, WebP.',
            'logo.max'        => 'The image must be smaller than 2MB.',
        ]);

        try {
            $file = $request->file('logo');

            // ★ Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename  = 'logo_' . Str::random(12) . '_' . time() . '.' . $extension;

            // ★ Destination: public_html/assets/logos/
            // public_path() returns the path to public_html/ on your server
            $destinationPath = public_path('assets/logos');

            // ★ Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // ★ Move the file directly to public_html/assets/logos/
            $file->move($destinationPath, $filename);

            // ★ Generate URL — asset() points to public_html/
            $url = asset('assets/logos/' . $filename);

            return response()->json([
                'success' => true,
                'url'     => $url,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save logo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /settings/widget/save-post-chat
     */
    public function savePostChat(Request $request)
    {
        $siteId = auth()->user()->site_id;

        $enabled    = $request->input('enabled', false);
        $formConfig = $request->input('form_config', []);

        if (is_string($formConfig)) {
            $decoded = json_decode($formConfig, true);
            $formConfig = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($formConfig)) {
            $formConfig = [];
        }

        $formConfig = array_slice($formConfig, 0, 20);

        $formConfig = array_map(function ($field) {
            if (!is_array($field)) return null;

            $clean = ['type' => $field['type'] ?? 'question'];

            if (isset($field['label'])) {
                $clean['label'] = substr((string) $field['label'], 0, 200);
            }
            if (isset($field['text'])) {
                $clean['text'] = substr((string) $field['text'], 0, 500);
            }
            if (isset($field['options']) && is_array($field['options'])) {
                $clean['options'] = array_slice(
                    array_map(fn($o) => substr((string) $o, 0, 100), $field['options']),
                    0, 20
                );
            }

            return $clean;
        }, $formConfig);

        $formConfig = array_filter($formConfig);

        PostChatForm::updateOrCreate(
            ['site_id' => $siteId],
            [
                'enabled'     => (bool) $enabled,
                'form_config' => array_values($formConfig),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Post-chat form saved!',
        ]);
    }

    /**
     * Helper: safely decode settings
     */
    private function decodeSettings($row)
    {
        if (!$row) return [];

        $settings = $row->settings;

        if (is_array($settings)) {
            return $settings;
        }

        if (is_string($settings)) {
            $decoded = json_decode($settings, true);

            if (is_string($decoded)) {
                $decoded2 = json_decode($decoded, true);
                if (is_array($decoded2)) {
                    return $decoded2;
                }
            }

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
    public function uploadEyeCatcher(Request $request)
    {
        $request->validate([
            'eye_catcher' => 'required|image|mimes:jpeg,png,gif,svg,webp|max:2048',
        ], [
            'eye_catcher.required' => 'Please select a file to upload.',
            'eye_catcher.image'    => 'The file must be an image.',
            'eye_catcher.mimes'    => 'Allowed types: JPEG, PNG, GIF, SVG, WebP.',
            'eye_catcher.max'      => 'The image must be smaller than 2MB.',
        ]);

        try {
            $file      = $request->file('eye_catcher');
            $extension = $file->getClientOriginalExtension();
            $filename  = 'ec_' . Str::random(12) . '_' . time() . '.' . $extension;

            $destinationPath = public_path('assets/eyecatchers');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $url = asset('assets/eyecatchers/' . $filename);

            return response()->json([
                'success' => true,
                'url'     => $url,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save image: ' . $e->getMessage(),
            ], 500);
        }
    }
}