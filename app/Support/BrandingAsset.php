<?php

namespace App\Support;

use Illuminate\Http\Request;

class BrandingAsset
{
    public static function url(?string $path, ?Request $request = null): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $path = trim((string) $path);

        // Mantém compatibilidade com URLs externas cadastradas anteriormente.
        if (
            str_starts_with($path, 'http://') ||
            str_starts_with($path, 'https://') ||
            str_starts_with($path, '//') ||
            str_starts_with($path, 'data:')
        ) {
            return $path;
        }

        $relative = ltrim($path, '/');

        // Normaliza valores locais que eventualmente já tenham sido salvos com /public/.
        if (str_starts_with($relative, 'public/')) {
            $relative = substr($relative, strlen('public/'));
        }

        // Para a identidade visual, nunca expomos a localização física do arquivo.
        // A URL termina em /serve para evitar que Nginx/Apache intercepte a requisição
        // pela extensão .png/.jpg/.ico antes que ela chegue ao Laravel.
        if (str_starts_with($relative, 'uploads/branding/')) {
            $filename = basename($relative);

            if (! preg_match('/^[A-Za-z0-9._-]+$/', $filename)) {
                return null;
            }

            return '/branding-assets/'.rawurlencode($filename).'/serve';
        }

        return '/'.$relative;
    }
}
