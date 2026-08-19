<?php

use App\Models\Setting;
use App\Models\Station;
use App\Services\Streaming\StreamingService;

if (! function_exists('station')) {
    function station(): ?Station
    {
        return Station::query()->first();
    }
}

if (! function_exists('radio_setting')) {
    function radio_setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('streaming_service')) {
    function streaming_service(): StreamingService
    {
        return app(StreamingService::class);
    }
}