<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shortcut;

class ShortcutController extends Controller
{
    public function index()
    {
        $shortcuts = Shortcut::where('site_id', auth()->user()->site_id)
                             ->where('created_by', auth()->id())
                             ->orderBy('created_at', 'desc')
                             ->get();

        return view('settings.shortcut.index', compact('shortcuts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shortcut'       => 'required|string|unique:shortcuts,shortcut,NULL,id,site_id,' . auth()->user()->site_id,
            'response_text'  => 'required|string',
            'tags'           => 'nullable|array',
            'auto_apply_tags'=> 'nullable|boolean',
            'is_shared'      => 'nullable|boolean',
        ]);

        // Ensure the shortcut starts with #
        $shortcut = ltrim($request->shortcut, '#');
        $request->merge(['shortcut' => '#' . $shortcut]);

        Shortcut::create([
            'shortcut'       => $request->shortcut,
            'response_text'  => $request->response_text,
            'tags'           => $request->tags,
            'auto_apply_tags'=> $request->boolean('auto_apply_tags'),
            'is_shared'      => $request->boolean('is_shared'),
            'site_id'        => auth()->user()->site_id,
            'created_by'     => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Shortcut created successfully.']);
    }

    public function update(Request $request, Shortcut $shortcut)
    {
        if ($shortcut->site_id !== auth()->user()->site_id || $shortcut->created_by !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'shortcut'       => 'required|string|unique:shortcuts,shortcut,' . $shortcut->id . ',id,site_id,' . auth()->user()->site_id,
            'response_text'  => 'required|string',
            'tags'           => 'nullable|array',
            'auto_apply_tags'=> 'nullable|boolean',
            'is_shared'      => 'nullable|boolean',
        ]);

        $shortcutVal = ltrim($request->shortcut, '#');
        $request->merge(['shortcut' => '#' . $shortcutVal]);

        $shortcut->update([
            'shortcut'       => $request->shortcut,
            'response_text'  => $request->response_text,
            'tags'           => $request->tags,
            'auto_apply_tags'=> $request->boolean('auto_apply_tags'),
            'is_shared'      => $request->boolean('is_shared'),
        ]);

        return response()->json(['success' => true, 'message' => 'Shortcut updated successfully.']);
    }

    public function destroy(Shortcut $shortcut)
    {
        if ($shortcut->site_id !== auth()->user()->site_id || $shortcut->created_by !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $shortcut->delete();

        return response()->json(['success' => true, 'message' => 'Shortcut deleted successfully.']);
    }

    public function getJson()
    {
        $shortcuts = Shortcut::where('site_id', auth()->user()->site_id)
                             ->where('created_by', auth()->id())
                             ->orWhere(function($query) {
                                 $query->where('site_id', auth()->user()->site_id)
                                       ->where('is_shared', true);
                             })
                             ->select('id', 'shortcut', 'response_text')
                             ->get();
                             
        return response()->json($shortcuts);
    }
}