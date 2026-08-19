<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class StationController extends Controller
{
    private const BRANDING_DIRECTORY = 'uploads/branding';

    public function edit(): Response
    {
        return Inertia::render('Admin/Station/Edit', [
            'station' => Station::query()->first() ?? new Station(),
        ]);
    }

    public function update(Request $request): RedirectResponse
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

            'logo_primary_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:8192'],
            'logo_small_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'favicon_upload' => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg,webp', 'max:2048'],
            'remove_logo_primary' => ['nullable', 'boolean'],
            'remove_logo_small' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],

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

        $brandingFields = [
            'logo_primary' => [
                'upload' => 'logo_primary_upload',
                'remove' => 'remove_logo_primary',
                'prefix' => 'logo-primary',
            ],
            'logo_small' => [
                'upload' => 'logo_small_upload',
                'remove' => 'remove_logo_small',
                'prefix' => 'logo-small',
            ],
            'favicon' => [
                'upload' => 'favicon_upload',
                'remove' => 'remove_favicon',
                'prefix' => 'favicon',
            ],
        ];

        foreach ($brandingFields as $column => $options) {
            if ($request->boolean($options['remove'])) {
                $this->deleteBrandingFile($station->{$column});
                $data[$column] = null;
            }

            if ($request->hasFile($options['upload'])) {
                $this->deleteBrandingFile($station->{$column});
                $data[$column] = $this->storeBrandingFile(
                    $request->file($options['upload']),
                    $options['prefix'],
                );
            }

            unset($data[$options['upload']], $data[$options['remove']]);
        }

        $data['dark_mode_enabled'] = $request->boolean('dark_mode_enabled');
        $data['floating_player_enabled'] = $request->boolean('floating_player_enabled');

        $station->update($data);

        return back()->with('success', 'Configurações da rádio salvas.');
    }

    protected function storeBrandingFile(UploadedFile $file, string $prefix): string
    {
        $directory = public_path(self::BRANDING_DIRECTORY);
        File::ensureDirectoryExists($directory, 0755, true);

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'png');
        $filename = $prefix.'-'.Str::uuid().'.'.$extension;

        $file->move($directory, $filename);

        return '/'.self::BRANDING_DIRECTORY.'/'.$filename;
    }

    protected function deleteBrandingFile(?string $path): void
    {
        if (! $path || ! str_starts_with($path, '/'.self::BRANDING_DIRECTORY.'/')) {
            return;
        }

        $filename = basename($path);
        $fullPath = public_path(self::BRANDING_DIRECTORY.'/'.$filename);

        if (File::isFile($fullPath)) {
            File::delete($fullPath);
        }
    }
}
