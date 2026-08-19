<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StationController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Admin/Station/Edit', [
            'station' => Station::query()->first() ?? new Station(),
        ]);
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $station = Station::query()->firstOrFail();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'frequency' => ['nullable', 'string', 'max:100'],
            'slogan' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'website_url' => ['nullable', 'url', 'max:2048'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'logo_primary' => ['nullable', 'url', 'max:2048'],
            'logo_small' => ['nullable', 'url', 'max:2048'],
            'favicon' => ['nullable', 'url', 'max:2048'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'background_color' => ['nullable', 'string', 'max:20'],
            'surface_color' => ['nullable', 'string', 'max:20'],
            'text_color' => ['nullable', 'string', 'max:20'],
            'muted_color' => ['nullable', 'string', 'max:20'],
            'border_color' => ['nullable', 'string', 'max:20'],
            'font_family' => ['nullable', 'string', 'max:100'],
            'button_style' => ['nullable', 'string', 'max:100'],
            'dark_mode_enabled' => ['boolean'],
            'floating_player_enabled' => ['boolean'],
        ]);

        $data['dark_mode_enabled'] = $request->boolean('dark_mode_enabled');
        $data['floating_player_enabled'] = $request->boolean('floating_player_enabled');

        $station->update($data);

        return back()->with('success', 'Configurações da rádio salvas.');
    }
}