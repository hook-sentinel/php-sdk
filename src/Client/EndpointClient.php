<?php

namespace HookSentinel\Client;

use HookSentinel\Objects\Endpoint;

/**
 * @internal
 */
class EndpointClient extends BaseApiClient
{

    public function create(Endpoint $endpoint): Endpoint
    {
        $response = $this->post('/api/endpoints', $endpoint->toArray());

        return new Endpoint($response->toArray());

    }
}