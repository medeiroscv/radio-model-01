<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Streaming\StreamingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StreamController extends Controller
{
    public function edit(StreamingService $streaming): Response
    {
        return Inertia::render('Admin/Stream/Edit', [
            'settings' => $streaming->settings(),
            'status' => $streaming->status(),
        ]);
    }

    public function update(Request $request, StreamingService $streaming): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'provider_type' => ['required', 'in:generic,icecast,shoutcast,azuracast'],
            'stream_url' => ['nullable', 'url', 'max:2048'],
            'stream_url_alt' => ['nullable', 'url', 'max:2048'],
            'mount_point' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'admin_url' => ['nullable', 'url', 'max:2048'],
            'stats_url' => ['nullable', 'url', 'max:2048'],
            'metadata_url' => ['nullable', 'url', 'max:2048'],
            'is_enabled' => ['boolean'],
            'history_enabled' => ['boolean'],
            'polling_interval' => ['nullable', 'integer', 'between:5,300'],
        ]);

        $data['is_enabled'] = $request->boolean('is_enabled');
        $data['history_enabled'] = $request->boolean('history_enabled');

        $streaming->settings()->update($data);
        cache()->forget('stream-status');

        return back()->with('success', 'Configurações de streaming salvas.');
    }
}