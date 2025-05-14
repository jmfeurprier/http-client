<?php

namespace Jmf\HttpClient;

use Jmf\HttpClient\Exception\HttpClientException;

interface HttpResponseInterface
{
    public function getHttpStatusCode(): int;

    /**
     * @return string[]
     */
    public function getHeaders(): iterable;

    public function getBody(): ?string;

    /**
     * @throws HttpClientException
     */
    public function getTransferDetail(string $key): mixed;

    public function hasTransferDetail(string $key): bool;

    /**
     * @return array<string, mixed>
     */
    public function getTransferDetails(): array;
}
