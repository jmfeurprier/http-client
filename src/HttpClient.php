<?php

namespace Jmf\HttpClient;

use CURLFile;
use CURLStringFile;
use Jmf\CurlClient\CurlClient;
use Jmf\CurlClient\CurlClientInterface;
use Jmf\CurlClient\CurlExecutionResult;
use Jmf\CurlClient\Exception\CurlClientException;
use Jmf\HttpClient\Exception\HttpClientException;
use Override;
use Webmozart\Assert\Assert;
use const CURLOPT_FILE;
use const CURLOPT_HEADER;
use const CURLOPT_NOBODY;
use const CURLOPT_RETURNTRANSFER;
use const PREG_SPLIT_NO_EMPTY;

readonly class HttpClient implements HttpClientInterface
{
    private HttpResponseFactoryInterface $responseFactory;

    private CurlClientInterface $curlClient;

    public function __construct(
        ?HttpResponseFactoryInterface $responseFactory = null,
        ?CurlClientInterface $curlClient = null,
    ) {
        $this->responseFactory = $responseFactory ?? new HttpResponseFactory();
        $this->curlClient      = $curlClient ?? new CurlClient();
    }

    #[Override]
    public function createRequestBuilder(): HttpRequestBuilderInterface
    {
        return new HttpRequestBuilder();
    }

    #[Override]
    public function createFile(
        string $filename,
        ?string $mimeType = null,
        ?string $postFilename = null,
    ): CURLFile {
        return $this->curlClient->createFile($filename, $mimeType, $postFilename);
    }

    #[Override]
    public function createStringFile(
        string $data,
        string $postname,
        string $mime = 'application/octet-stream',
    ): CURLStringFile {
        return $this->curlClient->createStringFile($data, $postname, $mime);
    }

    #[Override]
    public function execute(HttpRequestInterface $request): HttpResponseInterface
    {
        $result = $this->getCurlExecutionResult($request);

        return $this->responseFactory->create(
            $this->getHttpStatusCode($result),
            $this->getResponseHeaders($request, $result),
            $this->getResponseBody($request, $result),
            $result->getInfos(),
        );
    }

    /**
     * @throws HttpClientException
     */
    private function getCurlExecutionResult(HttpRequestInterface $request): CurlExecutionResult
    {
        $options = $request->getOptions();

        if ($this->isDownload($request)) {
            $options[CURLOPT_HEADER] = false;
            $options[CURLOPT_NOBODY] = false;
        } else {
            $options[CURLOPT_RETURNTRANSFER] = true;
        }

        try {
            $result = $this->curlClient->execute($options);
        } catch (CurlClientException $e) {
            throw new HttpClientException(
                message:  "Failed executing HTTP request.",
                code:     $e->getCode(),
                previous: $e,
            );
        }

        return $result;
    }

    /**
     * @throws HttpClientException
     */
    private function getHttpStatusCode(CurlExecutionResult $result): int
    {
        try {
            $httpStatusCode = $result->getInfo('http_code');
        } catch (CurlClientException $e) {
            throw new HttpClientException(
                message:  'Failed retrieving HTTP status code.',
                code:     $e->getCode(),
                previous: $e,
            );
        }

        Assert::integer($httpStatusCode);

        return $httpStatusCode;
    }

    /**
     * @return string[]
     *
     * @throws HttpClientException
     */
    private function getResponseHeaders(
        HttpRequestInterface $request,
        CurlExecutionResult $result,
    ): iterable {
        if (!$this->withHeaders($request)) {
            return [];
        }

        if ($this->withBody($request)) {
            $responseContent = $result->getResponseContent();

            Assert::string($responseContent);

            $position      = $this->getHeaderSize($result);
            $headerContent = substr($responseContent, 0, $position);
        } else {
            $headerContent = $result->getResponseContent();

            Assert::string($headerContent);
        }

        $headers = preg_split('|\\r\\n|', $headerContent, -1, PREG_SPLIT_NO_EMPTY);

        Assert::allStringNotEmpty($headers);

        return $headers;
    }

    /**
     * @throws HttpClientException
     */
    private function getResponseBody(
        HttpRequestInterface $request,
        CurlExecutionResult $result,
    ): ?string {
        if (!$this->withBody($request)) {
            return null;
        }

        $responseContent = $result->getResponseContent();

        if ($this->withHeaders($request)) {
            Assert::string($responseContent);

            $position = $this->getHeaderSize($result);

            return substr($responseContent, $position);
        }

        return $responseContent;
    }

    private function withHeaders(HttpRequestInterface $request): bool
    {
        if ($this->isDownload($request)) {
            return false;
        }

        $options = $request->getOptions();

        return (array_key_exists(CURLOPT_HEADER, $options) && $options[CURLOPT_HEADER]);
    }

    private function withBody(HttpRequestInterface $request): bool
    {
        if ($this->isDownload($request)) {
            return false;
        }

        $options = $request->getOptions();

        return !(array_key_exists(CURLOPT_NOBODY, $options) && $options[CURLOPT_NOBODY]);
    }

    private function isDownload(HttpRequestInterface $request): bool
    {
        $options = $request->getOptions();

        return (array_key_exists(CURLOPT_FILE, $options) && is_resource($options[CURLOPT_FILE]));
    }

    /**
     * @throws HttpClientException
     */
    private function getHeaderSize(CurlExecutionResult $result): int
    {
        try {
            $position = $result->getInfo('header_size');
        } catch (CurlClientException $e) {
            throw new HttpClientException(
                message:  'Failed retrieving HTTP headers size.',
                code:     $e->getCode(),
                previous: $e,
            );
        }

        Assert::integer($position);

        return $position;
    }
}
