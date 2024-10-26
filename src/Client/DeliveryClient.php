<?php

namespace HookSentinel\Client;


use Symfony\Component\HttpFoundation\Request;
use HookSentinel\Exception\InvalidSignatureException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DeliveryClient extends BaseApiClient
{


    public function log(string | array $response, int $responseCode,string $deliveryId) : ResponseInterface
    {
        return $this->post('/api/deliveries/'.$deliveryId.'/log',[
            'response' => $response,
            'responseCode' => $responseCode,
        ]);
    }


}