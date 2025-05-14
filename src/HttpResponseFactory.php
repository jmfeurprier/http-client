<?php

namespace Jmf\HttpClient;

use Override;

readonly class HttpResponseFactory implements HttpResponseFactoryInterface
{
    #[Override]
    public function create(
        int $httpStatusCode,
        iterable $headers,
        ?string $bodyContent,
        array $transferDetails = [],
    ): HttpResponseInterface {
        return new HttpResponse(
            $httpStatusCode,
            $headers,
            $bodyContent,
            $transferDetails,
        );
    }
}
