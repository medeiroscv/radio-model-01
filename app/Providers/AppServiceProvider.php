<?php

namespace App\Providers;

use App\Services\Streaming\StreamingService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StreamingService::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}