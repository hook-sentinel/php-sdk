<?php

namespace HookSentinel\Client;


use HookSentinel\Exception\InvalidSignatureException;
use Symfony\Contracts\HttpClient\ResponseInterface;

class EventClient extends BaseApiClient
{

    public ?string $endpointId = null;

    public ?string $endpointSignatureKey = null;

    public ?string $endpointSignatureAlgorithm = 'sha512';

    private ?ResponseInterface $latestResponse = null;


    public function send(array $content,?string $id = null, ?string $endpointSignatureKey = null) : ResponseInterface
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

        $this->latestResponse = $response;

        return $response;

    }


    public function logResponse(string | array $response, int $responseCode = 200,?string $deliveryId = null) : ResponseInterface
    {
        $deliveryId = $deliveryId ?? $this->latestResponse?->getHeaders()['x-delivery-id'][0] ?? null;

        if(null === $deliveryId) {
            throw new \Exception('Delivery ID is required, Did you forget to call send() before logResponse()?');
        }

        return $this->client->deliveries->log($response,$responseCode,$deliveryId);

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