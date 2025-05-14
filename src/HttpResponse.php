<?php

namespace Jmf\HttpClient;

use Jmf\HttpClient\Exception\HttpClientException;
use Override;
use Webmozart\Assert\Assert;

readonly class HttpResponse implements HttpResponseInterface
{
    /**
     * @param string[]             $headers
     * @param array<string, mixed> $transferDetails
     */
    public function __construct(
        private int $httpStatusCode,
        private iterable $headers,
        private ?string $bodyContent,
        private array $transferDetails = [],
    ) {
        Assert::allStringNotEmpty($headers);
    }

    #[Override]
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    #[Override]
    public function getHeaders(): iterable
    {
        return $this->headers;
    }

    #[Override]
    public function getBody(): ?string
    {
        return $this->bodyContent;
    }

    #[Override]
    public function getTransferDetail(string $key): mixed
    {
        if ($this->hasTransferDetail($key)) {
            return $this->transferDetails[$key];
        }

        throw new HttpClientException("Transfer detail '{$key}' is not defined.");
    }

    #[Override]
    public function hasTransferDetail(string $key): bool
    {
        return array_key_exists($key, $this->transferDetails);
    }

    #[Override]
    public function getTransferDetails(): array
    {
        return $this->transferDetails;
    }
}
