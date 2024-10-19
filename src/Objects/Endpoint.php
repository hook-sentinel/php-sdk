<?php

namespace HookSentinel\Objects;

class Endpoint extends BaseObject
{

    public ?string $id = null;

    // sent | received
    public ?string $endpointType = null;

    public ?string $name = null;

    public ?string $description = null;

    public ?string $url = null;

    public ?string $method = null;

    public ?string $response = null;

    public ?array $headers = null;


    public function toArray(): array
    {

        $vars = get_object_vars($this);

        foreach ($vars as $key => $value) {
            if ($value === null) {
                unset($this->$key);
            }
        }

        return $vars;

    }


}