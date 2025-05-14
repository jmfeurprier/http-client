<?php

namespace Jmf\HttpClient;

use Jmf\HttpClient\Exception\HttpClientException;
use Override;
use const CURLOPT_CONNECTTIMEOUT;
use const CURLOPT_COOKIE;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_FILE;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HEADER;
use const CURLOPT_HTTPGET;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_TIMEOUT;
use const CURLOPT_URL;

class HttpRequestBuilder implements HttpRequestBuilderInterface
{
    /**
     * @var array<int, mixed>
     */
    private array $options = [
        CURLOPT_HEADER => true,
    ];

    #[Override]
    public function setUrl(string $url): static
    {
        $this->setOption(CURLOPT_URL, $url);

        return $this;
    }

    #[Override]
    public function methodGet(): static
    {
        $this->unsetOption(CURLOPT_CUSTOMREQUEST);
        $this->unsetOption(CURLOPT_POST);
        $this->unsetOption(CURLOPT_POSTFIELDS);

        $this->setOption(CURLOPT_HTTPGET, true);

        return $this;
    }

    #[Override]
    public function methodPost(array $values = []): static
    {
        $this->unsetOption(CURLOPT_CUSTOMREQUEST);
        $this->unsetOption(CURLOPT_HTTPGET);
        $this->unsetOption(CURLOPT_POSTFIELDS);

        $this->setOption(CURLOPT_POST, true);

        if ([] !== $values) {
            $this->setOption(CURLOPT_POSTFIELDS, $values);
        }

        return $this;
    }

    #[Override]
    public function methodPut(array $values = []): static
    {
        $this->unsetOption(CURLOPT_HTTPGET);
        $this->unsetOption(CURLOPT_POST);
        $this->unsetOption(CURLOPT_POSTFIELDS);

        $this->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');

        if ([] !== $values) {
            $this->setOption(CURLOPT_POSTFIELDS, http_build_query($values));
        }

        return $this;
    }

    #[Override]
    public function methodDelete(array $values = []): static
    {
        $this->unsetOption(CURLOPT_HTTPGET);
        $this->unsetOption(CURLOPT_POST);
        $this->unsetOption(CURLOPT_POSTFIELDS);

        $this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

        if ([] !== $values) {
            $this->setOption(CURLOPT_POSTFIELDS, http_build_query($values));
        }

        return $this;
    }

    #[Override]
    public function methodCustom(
        string $method,
        array $values = [],
    ): static {
        $this->unsetOption(CURLOPT_HTTPGET);
        $this->unsetOption(CURLOPT_POST);
        $this->unsetOption(CURLOPT_POSTFIELDS);

        $this->setOption(CURLOPT_CUSTOMREQUEST, $method);

        if ([] === $values) {
            $this->setOption(CURLOPT_POSTFIELDS, http_build_query($values));
        }

        return $this;
    }

    #[Override]
    public function downloadTo(string $path): static
    {
        $fileResource = fopen($path, 'w');

        if (false === $fileResource) {
            throw new HttpClientException('Failed to open destination file for download.');
        }

        $this->setOption(CURLOPT_FILE, $fileResource);

        return $this;
    }

    #[Override]
    public function setConnectTimeout(int $timeout): static
    {
        $this->setOption(CURLOPT_CONNECTTIMEOUT, $timeout);

        return $this;
    }

    #[Override]
    public function setTransferTimeout(int $timeout): static
    {
        $this->setOption(CURLOPT_TIMEOUT, $timeout);

        return $this;
    }

    #[Override]
    public function followLocation(): static
    {
        return $this->setOption(CURLOPT_FOLLOWLOCATION, true);
    }

    #[Override]
    public function doNotFollowLocation(): static
    {
        return $this->setOption(CURLOPT_FOLLOWLOCATION, false);
    }

    #[Override]
    public function setHeaders(iterable $headers): static
    {
        return $this->setOption(CURLOPT_HTTPHEADER, $headers);
    }

    #[Override]
    public function setCookieString(string $string): static
    {
        $this->setOption(CURLOPT_COOKIE, $string);

        return $this;
    }

    #[Override]
    public function setOption(
        int $option,
        mixed $value,
    ): static {
        $this->options[$option] = $value;

        return $this;
    }

    private function unsetOption(int $option): void
    {
        unset($this->options[$option]);
    }

    #[Override]
    public function build(): HttpRequestInterface
    {
        return new HttpRequest($this->options);
    }
}
