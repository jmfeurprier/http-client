<?php

namespace Jmf\HttpClient;

use Override;

readonly class HttpRequest implements HttpRequestInterface
{
    /**
     * @param array<int, mixed> $options
     */
    public function __construct(
        private array $options,
    ) {
    }

    #[Override]
    public function getOptions(): array
    {
        return $this->options;
    }
}
