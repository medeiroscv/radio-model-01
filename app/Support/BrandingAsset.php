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

        // Normaliza valores que eventualmente já tenham sido salvos com /public/.
        if (str_starts_with($relative, 'public/')) {
            $relative = substr($relative, strlen('public/'));
        }

        // Fora da área de branding, apenas normaliza para uma URL absoluta no domínio.
        if (! str_starts_with($relative, 'uploads/branding/')) {
            return '/'.$relative;
        }

        $publicFile = public_path($relative);

        if (! is_file($publicFile)) {
            return '/'.$relative;
        }

        $request ??= request();
        $documentRoot = $request?->server('DOCUMENT_ROOT');
        $documentRootReal = $documentRoot ? realpath((string) $documentRoot) : false;
        $publicRootReal = realpath(public_path());

        // Instalação Laravel convencional: document root já aponta para /public.
        if ($documentRootReal && $publicRootReal && $documentRootReal === $publicRootReal) {
            return '/'.$relative;
        }

        if ($documentRootReal) {
            $nativeRelative = str_replace('/', DIRECTORY_SEPARATOR, $relative);
            $directFile = $documentRootReal.DIRECTORY_SEPARATOR.$nativeRelative;

            // Caso exista uma cópia física diretamente no document root, usa-a.
            if (is_file($directFile)) {
                return '/'.$relative;
            }

            $nestedPublicFile = $documentRootReal
                .DIRECTORY_SEPARATOR.'public'
                .DIRECTORY_SEPARATOR.$nativeRelative;

            // Hestia/hosting compartilhado servindo a raiz do projeto.
            if (is_file($nestedPublicFile)) {
                return '/public/'.$relative;
            }
        }

        // Fallback seguro. Em instalações padrão, esta é a URL esperada.
        return '/'.$relative;
    }
}
