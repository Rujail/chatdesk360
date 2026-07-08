<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chat\ChatConfigController;
use App\Http\Controllers\Chat\VisitorController;
use App\Http\Controllers\Api\ChatAuthenticationController;
use App\Http\Controllers\Api\WidgetApiController;

use App\Http\Controllers\Settings\BannedCustomersController;
 
// Widget config
Route::post('/chat/config', [ChatConfigController::class, 'getConfig']);
 
// Visitor tracking — existing
Route::post('/visitor/track',      [VisitorController::class, 'track']);
Route::post('/visitor/page-start', [VisitorController::class, 'pageStart']);
Route::post('/visitor/page',       [VisitorController::class, 'trackPage']);
Route::post('/visitor/chat-start', [VisitorController::class, 'chatStart']);
 
// ── NEW ROUTES ────────────────────────────────────────────────────────────────
Route::post('/visitor/leave',      [VisitorController::class, 'leave']);
Route::post('/visitor/heartbeat',  [VisitorController::class, 'heartbeat']);
Route::post('/visitor/status', [VisitorController::class, 'updateStatus']);

Route::post('/visitor/assign', [VisitorController::class, 'assignAgent']);


Route::post('/chat/authenticate', [ChatAuthenticationController::class, 'authenticate']);


Route::post('/visitor/check-ban', [BannedCustomersController::class, 'checkBan']);



Route::get('chat-files/{siteId}/{filename}', function ($siteId, $filename) {
    $path = storage_path('app/public/chat-files/' . $siteId . '/' . $filename);
    if (!file_exists($path)) abort(404);
    return response()->file($path);
});

Route::get('storage/chat-files/{siteId}/{filename}', function ($siteId, $filename) {
    $path = storage_path('app/public/chat-files/' . $siteId . '/' . $filename);
    if (!file_exists($path)) abort(404);
    return response()->file($path);
});
Route::post('/widget/upload-file', [WidgetApiController::class, 'uploadFile']);

Route::post('/widget/send-transcript', [WidgetApiController::class, 'sendTranscript']);

/* ── Public widget API — called from widget.js on customer sites ── */
Route::prefix('widget')->group(function () {
    Route::post('/settings', [WidgetApiController::class, 'getSettings']);
    Route::post('/post-chat-config', [WidgetApiController::class, 'getPostChatConfig']);
    Route::post('/post-chat-response', [WidgetApiController::class, 'savePostChatResponse']);
});

// Dashboard API Endpoints
