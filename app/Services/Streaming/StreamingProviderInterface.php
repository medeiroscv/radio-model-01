<?php

namespace App\Services\Streaming;

interface StreamingProviderInterface
{
    /**
     * Configura o provider com as configurações atuais de streaming.
     */
    public function configure(array $config): void;

    /**
     * Verifica se o stream está online.
     */
    public function isOnline(): bool;

    /**
     * Obtém os metadados atuais (artista, música, capa, etc).
     */
    public function getMetadata(): array;

    /**
     * Obtém o histórico recente de músicas tocadas.
     */
    public function getHistory(int $limit = 10): array;

    /**
     * Obtém a URL do stream para o player.
     */
    public function getStreamUrl(): ?string;

    /**
     * Obtém a URL alternativa do stream.
     */
    public function getAlternativeStreamUrl(): ?string;

    /**
     * Obtém informações de listeners atuais.
     */
    public function getListeners(): ?int;

    /**
     * Obtém o nome do provider.
     */
    public function getName(): string;
}