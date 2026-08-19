<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('testing')) {
            return $next($request);
        }

        $alreadyInstalled = false;

        if (file_exists(base_path('.installed'))) {
            $alreadyInstalled = true;
        }

        if (! $alreadyInstalled) {
            try {
                DB::connection()->getPdo();
                $alreadyInstalled = \App\Models\Station::where('is_installed', true)->exists();
            } catch (\Throwable $e) {
                $alreadyInstalled = false;
            }
        }

        if (! $alreadyInstalled && ! $request->is('install*')) {
            return redirect()->route('installer.index');
        }

        if ($alreadyInstalled && $request->is('install*')) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}