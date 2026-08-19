<?php

namespace App\Services\Streaming;

class ShoutcastProvider extends GenericStreamProvider
{
    public function isOnline(): bool
    {
        $url = $this->getAdminStatsUrl();

        if (! $url) {
            return parent::isOnline();
        }

        try {
            $response = $this->fetch($url);

            return str_contains($response, 'songtitle') || str_contains($response, '<SHOUTCASTSERVER');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getMetadata(): array
    {
        $url = $this->getAdminStatsUrl();
        if (! $url) {
            return parent::getMetadata();
        }

        try {
            $response = $this->fetch($url);
            $parsed = $this->parse7html($response);

            if (! empty($parsed['songtitle'])) {
                [$artist, $title] = $this->splitArtistTitle($parsed['songtitle']);

                return [
                    'artist' => $artist,
                    'title' => $title,
                    'album' => null,
                    'cover' => null,
                    'program' => null,
                    'presenter' => null,
                ];
            }
        } catch (\Throwable $e) {
            // silenciado
        }

        return parent::getMetadata();
    }

    public function getListeners(): ?int
    {
        $url = $this->getAdminStatsUrl();
        if (! $url) {
            return null;
        }

        try {
            $response = $this->fetch($url);
            $parsed = $this->parse7html($response);

            return isset($parsed['currentlisteners']) ? (int) $parsed['currentlisteners'] : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getName(): string
    {
        return 'shoutcast';
    }

    protected function getAdminStatsUrl(): ?string
    {
        $base = $this->config['admin_url'] ?? null;

        if (! $base) {
            return null;
        }

        $scheme = parse_url($base, PHP_URL_SCHEME) ?: 'http';
        $host = parse_url($base, PHP_URL_HOST);
        $port = parse_url($base, PHP_URL_PORT) ?: 8000;

        return "{$scheme}://{$host}:{$port}/admin.cgi?mode=viewxml";
    }

    protected function fetch(string $url): string
    {
        $username = $this->config['username'] ?? 'admin';
        $password = $this->config['password'] ?? '';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERPWD => "{$username}:{$password}",
            CURLOPT_USERAGENT => 'RadioCMS-Shoutcast/1.0',
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Falha ao buscar status do Shoutcast');
        }

        return $response;
    }

    protected function parse7html(string $response): array
    {
        $result = [];
        if (preg_match_all('/(\w+)\s*=\s*"([^"]*)"/', $response, $matches)) {
            foreach ($matches[1] as $i => $key) {
                $result[$key] = $matches[2][$i];
            }
        }

        return $result;
    }

    protected function splitArtistTitle(string $raw): array
    {
        $raw = trim($raw);
        if (str_contains($raw, ' - ')) {
            [$artist, $title] = explode(' - ', $raw, 2);

            return [trim($artist), trim($title)];
        }

        return [null, $raw];
    }
}