<?php

namespace HookSentinel\Client;


use Symfony\Component\HttpFoundation\Request;
use HookSentinel\Exception\InvalidSignatureException;

class EventClient extends BaseApiClient
{

    public ?string $endpointId = null;

    public ?string $endpointSignatureKey = null;

    public ?string $endpointSignatureAlgorithm = 'sha512';


    public function send(array $content,?string $id = null, ?string $endpointSignatureKey = null) : string
    {
        $endpointSignatureKey = $endpointSignatureKey ?? $this->endpointSignatureKey;
        $id = $id ?? $this->endpointId;

        if(!$id){
            throw new \Exception('Endpoint ID is required, please set it in the client or pass it as an argument');
        }

        if(!$endpointSignatureKey) {
            throw new \Exception('Endpoint Signature is required, please set it in the client or pass it as an argument');
        }

        [$endpointSignature,$timestamp] = $this->createSignature($content,$endpointSignatureKey);


        $response = $this->post('/api/endpoints/'.$id.'/events', $content,[
            'headers' => [
                'X-Signature' => $endpointSignature,
                'X-Timestamp' => $timestamp
            ]
        ]);

        return $response->getContent();

    }


    function getEventData(string $signatureSecret) : array
    {
        $headers = getallheaders();

        $timestamp = $headers['X-Timestamp'] ?? null;
        $signature = $headers['X-Signature'] ?? null;

        $content = file_get_contents('php://input');

        $message = $timestamp . '.' . $content;

        $expectedSignature = hash_hmac($this->endpointSignatureAlgorithm, $message, $signatureSecret);

        if ($signature !== $expectedSignature) {
            throw new InvalidSignatureException('Invalid Signature');
        }

        return json_decode($content, true);
    }



    private function createSignature(array $content,string $signatureKey) : array
    {
        $timestamp = time();
        $message = $timestamp . '.' . json_encode($content);

        $signature = hash_hmac($this->endpointSignatureAlgorithm, $message, $signatureKey);

        return [$signature,$timestamp];

    }


}