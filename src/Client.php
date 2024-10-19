<?php

namespace HookSentinel;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use HookSentinel\Client\EndpointClient;
use HookSentinel\Client\EventClient;

class Client
{

    public EndpointClient $endpoints;

    public EventClient $events;


    public function __construct(
        private readonly ?string $apiKey,
        ?HttpClientInterface    $httpClient = null,
        private readonly array  $options = [],
    )
    {
        $httpClient = $httpClient ?? HttpClient::createForBaseUri($this->options['baseUrl'] ?? 'https://api.HookSentinel.com');

        $this->endpoints = new EndpointClient($httpClient,$this->apiKey);
        $this->events = new EventClient($httpClient,$this->apiKey);

    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

}
