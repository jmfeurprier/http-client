<?php

namespace Jmf\HttpClient;

interface HttpResponseFactoryInterface
{
    /**
     * @param string[]             $headers
     * @param array<string, mixed> $transferDetails
     */
    public function create(
        int $httpStatusCode,
        iterable $headers,
        ?string $bodyContent,
        array $transferDetails = [],
    ): HttpResponseInterface;
}
