<?php

namespace App\Http\Middleware;

use App\Models\Banner;
use App\Models\Menu;
use App\Models\Setting;
use App\Models\SocialLink;
use App\Models\Station;
use App\Services\Streaming\StreamingService;
use App\Support\BrandingAsset;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\DB;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    protected function isInstalled(): bool
    {
        if (file_exists(base_path('.installed'))) {
            return true;
        }

        try {
            DB::connection()->getPdo();

            return Station::where('is_installed', true)->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function share(Request $request): array
    {
        $installed = $this->isInstalled();

        $station = null;
        $settings = [];
        $socialLinks = [];
        $mainMenu = null;
        $streamStatus = null;
        $banners = [];

        if ($installed) {
            $station = Station::query()->first();
            $settings = Setting::allCached();
            $socialLinks = SocialLink::where('is_active', true)->orderBy('sort_order')->get(['platform', 'url']);
            $mainMenu = Menu::where('slug', 'main-menu')->with('items.children')->first();
            $streamStatus = app(StreamingService::class)->status();
            $banners = Banner::active()->orderBy('sort_order')->get([
                'id', 'title', 'image_desktop', 'image_mobile', 'url', 'position',
            ]);
        }

        $pageTitle = $station?->name ? "{$station->name}" : config('app.name', 'RadioCMS');
        $pageDescription = $station?->slogan ?? '';

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()?->load('roles'),
                'permissions' => $request->user()?->getAllPermissions()->pluck('name') ?? [],
                'roles' => $request->user()?->getRoleNames() ?? [],
            ],
            'station' => $station ? [
                'name' => $station->name,
                'frequency' => $station->frequency,
                'slogan' => $station->slogan,
                'city' => $station->city,
                'state' => $station->state,
                'country' => $station->country,
                'timezone' => $station->timezone,
                'website_url' => $station->website_url,
                'email' => $station->email,
                'phone' => $station->phone,
                'whatsapp' => $station->whatsapp,
                'address' => $station->address,
                'logo_primary' => BrandingAsset::url($station->logo_primary, $request),
                'logo_small' => BrandingAsset::url($station->logo_small, $request),
                'favicon' => BrandingAsset::url($station->favicon, $request),
                'primary_color' => $station->primary_color,
                'secondary_color' => $station->secondary_color,
                'accent_color' => $station->accent_color,
                'background_color' => $station->background_color,
                'surface_color' => $station->surface_color,
                'text_color' => $station->text_color,
                'muted_color' => $station->muted_color,
                'border_color' => $station->border_color,
                'font_family' => $station->font_family,
                'button_style' => $station->button_style,
                'dark_mode_enabled' => $station->dark_mode_enabled,
                'floating_player_enabled' => $station->floating_player_enabled,
                'is_installed' => $station->is_installed,
            ] : null,
            'appSettings' => $settings,
            'socialLinks' => $socialLinks,
            'mainMenu' => $mainMenu,
            'streamStatus' => $streamStatus,
            'activeBanners' => $banners,
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'csrf_token' => csrf_token(),
        ];
    }
}
