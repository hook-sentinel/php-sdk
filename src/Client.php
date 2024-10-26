<?php

namespace HookSentinel;

use HookSentinel\Client\DeliveryClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use HookSentinel\Client\EndpointClient;
use HookSentinel\Client\EventClient;

class Client
{

    public EndpointClient $endpoints;

    public EventClient $events;

    public DeliveryClient $deliveries;


    public function __construct(
        private readonly ?string $apiKey,
        ?HttpClientInterface    $httpClient = null,
        private readonly array  $options = [],
    )
    {
        $httpClient = $httpClient ?? HttpClient::createForBaseUri($this->options['baseUrl'] ?? 'https://api.hooksentinel.com');

        $this->endpoints = new EndpointClient($httpClient,$this->apiKey);
        $this->endpoints->client = $this;

        $this->events = new EventClient($httpClient,$this->apiKey);
        $this->events->client = $this;

        $this->deliveries = new DeliveryClient($httpClient,$this->apiKey);
        $this->deliveries->client = $this;

    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

}
