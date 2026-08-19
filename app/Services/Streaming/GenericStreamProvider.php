<?php

namespace App\Services\Streaming;

class GenericStreamProvider implements StreamingProviderInterface
{
    protected array $config = [];

    public function configure(array $config): void
    {
        $this->config = $config;
    }

    public function isOnline(): bool
    {
        $url = $this->getStreamUrl();

        if (! $url) {
            return false;
        }

        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_NOBODY => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 8,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'RadioCMS-HealthCheck/1.0',
            ]);

            curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $status >= 200 && $status < 400;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getMetadata(): array
    {
        return [
            'artist' => null,
            'title' => null,
            'album' => null,
            'cover' => null,
            'program' => null,
            'presenter' => null,
        ];
    }

    public function getHistory(int $limit = 10): array
    {
        return [];
    }

    public function getStreamUrl(): ?string
    {
        return $this->config['stream_url'] ?? null;
    }

    public function getAlternativeStreamUrl(): ?string
    {
        return $this->config['stream_url_alt'] ?? null;
    }

    public function getListeners(): ?int
    {
        return null;
    }

    public function getName(): string
    {
        return 'generic';
    }
}