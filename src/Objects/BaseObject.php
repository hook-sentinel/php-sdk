<?php

namespace HookSentinel\Objects;

use ApiPlatform\Metadata\ApiProperty;
use function Symfony\Component\String\u;

abstract class BaseObject
{

    public function __construct(array $data = [])
    {
        $this->setEntityData($data);
    }

    #[ApiProperty(readable: false, writable: false)]
    public function setEntityData(array $data = []): void
    {
        foreach ($data as $key => $val) {
            $this->setEntityValue($key, $val);
        }
    }

    #[ApiProperty(readable: false, writable: false)]
    public function setEntityValue(string $key, string|int|bool|array|object|null $value): void
    {
        $vars = get_object_vars($this);
        $camelizeKey = $this->camelize($key);

        // Setter found -> $this->setMyProperty($value);
        $setter = "set$camelizeKey";
        if (method_exists($this, $setter)) {
            $this->$setter($value);
            return;
        }

        // Adder found -> $this->addMyProperty($value);
        $setter = "add$camelizeKey";
        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        }


        // Property found -> $this->myProperty = $value;
        if (array_key_exists($camelizeKey, $vars)) {
            $this->$camelizeKey = $value;
        }

        // Property found -> $this->myProperty = $value;
        if (array_key_exists($key, $vars)) {
            $this->$key = $value;
        }
    }

    private function camelize(string $key): string
    {
        // Stolen from Symfony\Component\String\Inflector\EnglishInflector::camelize
        return str_replace(' ', '', preg_replace_callback('/\b.(?![A-Z]{2,})/u', static function ($m) use (&$i) {
            return 1 === ++$i ? ('İ' === $m[0] ? 'i̇' : mb_strtolower($m[0], 'UTF-8')) : mb_convert_case($m[0], \MB_CASE_TITLE, 'UTF-8');
        }, preg_replace('/[^\pL0-9]++/u', ' ', $key)));
    }

}