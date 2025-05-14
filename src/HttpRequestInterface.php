<?php

namespace Jmf\HttpClient;

interface HttpRequestInterface
{
    /**
     * @return array<int, mixed>
     */
    public function getOptions(): array;
}
