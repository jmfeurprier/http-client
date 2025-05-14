<?php

namespace Jmf\HttpClient;

use Jmf\HttpClient\Exception\HttpClientException;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    private HttpClient $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = new HttpClient();
    }

    /**
     * @throws HttpClientException
     */
    public function testSimpleGet(): void
    {
        $request = $this->httpClient->createRequestBuilder()
            ->setUrl('http://httpbin.org/get')
            ->build()
        ;

        $result = $this->httpClient->execute($request);

        self::assertIsString($result->getBody());
        self::assertSame(200, $result->getHttpStatusCode());
    }

    /**
     * @throws HttpClientException
     */
    public function testSimplePost(): void
    {
        $request = $this->httpClient->createRequestBuilder()
            ->setUrl('http://httpbin.org/post')
            ->methodPost()
            ->build()
        ;

        $result = $this->httpClient->execute($request);

        self::assertIsString($result->getBody());
        self::assertSame(200, $result->getHttpStatusCode());
    }
}
