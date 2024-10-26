<?php

namespace HookSentinel\Client;

use HookSentinel\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class BaseApiClient
{


    public ?Client $client;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string              $apiKey
    )
    {

    }

    public function post(string $url, array $data, array $options = []): ResponseInterface
    {

        $headers = [
            ...$options['headers'] ?? [],
        ];

        if(!($options['anonymous'] ?? false)) {
            $headers['X-Api-Key'] = $this->apiKey;
        }

        return $this->httpClient->request('POST', $url, [
                'headers' => $headers,
                'json'    => $data,
            ]
        );
    }


}