<?php

namespace App\Services\Streaming;

use InvalidArgumentException;

class StreamingProviderFactory
{
    public static function create(string $type): StreamingProviderInterface
    {
        return match ($type) {
            'icecast' => new IcecastProvider,
            'shoutcast' => new ShoutcastProvider,
            'azuracast' => new AzuraCastProvider,
            'generic', 'mp3', 'aac', 'hls' => new GenericStreamProvider,
            default => throw new InvalidArgumentException("Provider de streaming desconhecido: {$type}"),
        };
    }
}