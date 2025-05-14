# HTTP client

Simple HTTP client using cURL internally.

## Installation & Requirements

Install with [Composer](https://getcomposer.org):

```shell script
composer require jmf/http-client
```

## Usage

### Simple GET request

```php
<?php

use Jmf\HttpClient\HttpClient;

$httpClient = new HttpClient();

$request = $httpClient->createRequestBuilder()
    ->methodGet()
    ->setUrl('http://localhost/index.html')
    ->build()
;

$response = $httpClient->execute($request);

$httpStatusCode = $response->getHttpStatusCode();
$content        = $response->getBody();

```

### Simple POST request

```php
<?php

use Jmf\HttpClient\HttpClient;

$httpClient = new HttpClient();

$request = $httpClient->createRequestBuilder()
    ->methodPost(
        [
            'title'   => 'test article',
            'content' => 'article content ...',
            'picture' => $httpClient->createFile('/path/to/file.jpg')
        ]
    )
    ->setUrl('http://localhost/create-article.php')
    ->build()
;

$response = $httpClient->execute($request);

$httpStatusCode = $response->getHttpStatusCode();
$content        = $response->getBody();

```
