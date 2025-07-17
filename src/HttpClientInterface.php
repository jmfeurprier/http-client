<?php

namespace Jmf\HttpClient;

use CURLFile;
use CURLStringFile;
use Jmf\HttpClient\Exception\HttpClientException;

interface HttpClientInterface
{
    public function createRequestBuilder(): HttpRequestBuilderInterface;

    public function createFile(
        string $filename,
        ?string $mimeType = null,
        ?string $postFilename = null,
    ): CURLFile;

    public function createStringFile(
        string $data,
        string $postname,
        string $mime = 'application/octet-stream',
    ): CURLStringFile;

    /**
     * @throws HttpClientException
     */
    public function execute(HttpRequestInterface $request): HttpResponseInterface;
}
