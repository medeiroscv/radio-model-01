<?php

namespace App\Services\Streaming;

class AzuraCastProvider extends GenericStreamProvider
{
    public function isOnline(): bool
    {
        $url = $this->getNowPlayingUrl();

        if (! $url) {
            return parent::isOnline();
        }

        try {
            $data = $this->fetchJson($url);

            return isset($data['is_online']) ? (bool) $data['is_online'] : true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getMetadata(): array
    {
        $url = $this->getNowPlayingUrl();
        if (! $url) {
            return parent::getMetadata();
        }

        try {
            $data = $this->fetchJson($url);
            $now = $data['now_playing'] ?? [];

            $song = $now['song'] ?? [];
            $artist = $song['artist'] ?? null;
            $title = $song['title'] ?? null;
            $cover = ! empty($song['art']) ? $this->buildCoverUrl($song['art']) : null;

            $live = $data['live'] ?? [];
            $streamer = $live['is_live'] ?? false ? ($live['streamer_name'] ?? null) : null;

            return [
                'artist' => $artist,
                'title' => $title,
                'album' => $song['album'] ?? null,
                'cover' => $cover,
                'program' => null,
                'presenter' => $streamer,
            ];
        } catch (\Throwable $e) {
            return parent::getMetadata();
        }
    }

    public function getHistory(int $limit = 10): array
    {
        $url = $this->getNowPlayingUrl();
        if (! $url) {
            return [];
        }

        try {
            $data = $this->fetchJson($url);
            $history = array_slice($data['song_history'] ?? [], 0, $limit);

            return array_map(function ($item) {
                $song = $item['song'] ?? [];

                return [
                    'artist' => $song['artist'] ?? null,
                    'title' => $song['title'] ?? null,
                    'album' => $song['album'] ?? null,
                    'cover' => ! empty($song['art']) ? $this->buildCoverUrl($song['art']) : null,
                    'played_at' => $item['played_at'] ?? null,
                ];
            }, $history);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getListeners(): ?int
    {
        $url = $this->getNowPlayingUrl();
        if (! $url) {
            return null;
        }

        try {
            $data = $this->fetchJson($url);
            $listeners = $data['listeners'] ?? [];

            return (int) ($listeners['current'] ?? 0);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getName(): string
    {
        return 'azuracast';
    }

    protected function getNowPlayingUrl(): ?string
    {
        $base = $this->config['admin_url'] ?? null;
        $streamUrl = $this->getStreamUrl();

        if ($base) {
            $base = rtrim($base, '/');

            return "{$base}/api/nowplaying";
        }

        if ($streamUrl) {
            $parts = parse_url($streamUrl);
            if (isset($parts['host'])) {
                $port = $parts['port'] ?? ($parts['scheme'] === 'https' ? 443 : 80);

                return "{$parts['scheme']}://{$parts['host']}:{$port}/api/nowplaying";
            }
        }

        return null;
    }

    protected function fetchJson(string $url): array
    {
        $headers = [];
        if (! empty($this->config['api_key'])) {
            $headers[] = 'X-Api-Key: '.$this->config['api_key'];
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERAGENT => 'RadioCMS-AzuraCast/1.0',
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Falha ao buscar now playing do AzuraCast');
        }

        $data = json_decode($response, true);
        if (! is_array($data)) {
            throw new \RuntimeException('JSON inválido do AzuraCast');
        }

        return $data;
    }

    protected function buildCoverUrl(string $artPath): string
    {
        if (str_starts_with($artPath, 'http')) {
            return $artPath;
        }

        $base = $this->config['admin_url'] ?? $this->getStreamUrl();
        if (! $base) {
            return $artPath;
        }

        $base = rtrim($base, '/');

        return "{$base}{$artPath}";
    }
}