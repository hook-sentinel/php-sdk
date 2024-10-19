
# HookSentinel PHP SDK

HookSentinel PHP SDK is a simple and intuitive library that helps you integrate with the HookSentinel API to manage endpoints and send events.

## Installation

Install the SDK via Composer:

```bash
composer require hook-sentinel/hook-sentinel-php-sdk
```

## Usage

### Initialize the Client

Before making any requests, initialize the client with your API key and the base URL of your instance.

```php
use HookSentinel\Client;

$apiKey = 'your-api-key';
$client = new Client($apiKey, 'https://your-instance-url');
```

### Create an Endpoint

Create a new webhook endpoint by providing its details:

```php
use HookSentinel\Objects\Endpoint;

$endpoint = new Endpoint();
$endpoint->name = 'Webhook Name';
$endpoint->endpointType = 'sent';
$endpoint->url = 'https://example.com/webhooks';
$endpoint->description = 'My Webhook';
$endpoint->method = 'POST';
$endpoint->response = 'response data';

$response = $client->endpoints->create($endpoint);
```

### Send an Event

Send an event to your webhook endpoint:

```php
$client->events->endpointSignatureKey = "your-signature-key";
$client->events->endpointId = 'your-endpoint-id';

$client->events->send([
    'foo' => 'bar',
    'message' => 'This is a test event'
]);
```

### Retrieve Event Data

Get the details of a received event by providing the secret key:

```php
$secretKey = 'your-secret-key';
$eventData = $client->events->getEventData($secretKey);

print_r($eventData);
```

## Handling Errors

The SDK throws exceptions for any errors. You can catch and handle them like this:

```php
use Symfony\Component\HttpClient\Exception\ClientException;

try {
    $response = $client->endpoints->create($endpoint);
} catch (ClientException $exception) {
    echo $exception->getResponse()->getContent(false);
}
```

## License

This project is licensed under the MIT License.