<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BrandingAssetController extends Controller
{
    public function __invoke(string $filename): BinaryFileResponse
    {
        if ($filename !== basename($filename) || ! preg_match('/^[A-Za-z0-9._-]+$/', $filename)) {
            abort(404);
        }

        $path = public_path('uploads/branding/'.$filename);

        if (! File::isFile($path) || ! is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
