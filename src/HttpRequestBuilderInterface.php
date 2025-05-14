<?php

namespace Jmf\HttpClient;

use Jmf\HttpClient\Exception\HttpClientException;

interface HttpRequestBuilderInterface
{
    public function setUrl(string $url): static;

    public function methodGet(): static;

    /**
     * @param array<string, mixed> $values
     */
    public function methodPost(array $values = []): static;

    /**
     * @param array<string, mixed> $values
     */
    public function methodPut(array $values = []): static;

    /**
     * @param array<string, mixed> $values
     */
    public function methodDelete(array $values = []): static;

    /**
     * @param array<string, mixed> $values
     */
    public function methodCustom(
        string $method,
        array $values = [],
    ): static;

    /**
     * @throws HttpClientException
     */
    public function downloadTo(string $path): static;

    public function setConnectTimeout(int $timeout): static;

    public function setTransferTimeout(int $timeout): static;

    public function followLocation(): static;

    public function doNotFollowLocation(): static;

    /**
     * @param string[] $headers
     */
    public function setHeaders(iterable $headers): static;

    public function setCookieString(string $string): static;

    public function setOption(
        int $option,
        mixed $value,
    ): static;

    public function build(): HttpRequestInterface;
}
