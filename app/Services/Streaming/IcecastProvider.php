<?php

namespace App\Services\Streaming;

class IcecastProvider extends GenericStreamProvider
{
    public function isOnline(): bool
    {
        $url = $this->config['stats_url'] ?? $this->getStreamUrl();

        if (! $url) {
            return false;
        }

        try {
            $response = $this->fetch($url);
            $data = $this->parseStatusXml($response);

            return isset($data['icestats']);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getMetadata(): array
    {
        try {
            $url = $this->config['stats_url'] ?? null;
            if (! $url) {
                return parent::getMetadata();
            }

            $response = $this->fetch($url);
            $data = $this->parseStatusXml($response);

            $source = $data['icestats']['source'] ?? null;
            if (is_array($source) && isset($source['title'])) {
                [$artist, $title] = $this->splitArtistTitle($source['title']);

                return [
                    'artist' => $artist,
                    'title' => $title,
                    'album' => $source['album'] ?? null,
                    'cover' => $source['art'] ?? null,
                    'program' => null,
                    'presenter' => null,
                ];
            }
        } catch (\Throwable $e) {
            // Silencia erros de parse
        }

        return parent::getMetadata();
    }

    public function getListeners(): ?int
    {
        try {
            $url = $this->config['stats_url'] ?? null;
            if (! $url) {
                return null;
            }

            $response = $this->fetch($url);
            $data = $this->parseStatusXml($response);
            $source = $data['icestats']['source'] ?? null;

            if (is_array($source)) {
                return (int) ($source['listeners'] ?? 0);
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getName(): string
    {
        return 'icecast';
    }

    protected function fetch(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'RadioCMS-Icecast/1.0',
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Falha ao buscar status do Icecast');
        }

        return $response;
    }

    protected function parseStatusXml(string $xml): array
    {
        $parsed = simplexml_load_string($xml);
        if ($parsed === false) {
            throw new \RuntimeException('XML inválido');
        }

        return json_decode(json_encode($parsed), true);
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