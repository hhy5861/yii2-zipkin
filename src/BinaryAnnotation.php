<?php

namespace mike\zipkin;

class BinaryAnnotation
{
    const TYPE_BOOL = 0;

    const TYPE_BYTES = 1;

    const TYPE_I16 = 2;

    const TYPE_I32 = 3;

    const TYPE_I64 = 4;

    const TYPE_DOUBLE = 5;

    const TYPE_STRING = 6;

    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var int
     */
    private $type;

    /**
     * @var Endpoint|null
     */
    private $endpoint;

    /**
     * @param string $key
     * @param mixed $value
     * @param int $type
     * @param Endpoint|null $endpoint
     */
    public function __construct($key, $value, $type, Endpoint $endpoint = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Endpoint|null
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [
            'key' => $this->key,
            'value' => $this->value,
        ];

        if ($this->endpoint) {
            $data['endpoint'] = $this->endpoint->toArray();
        }

        return $data;
    }

    /**
     * @param $key
     * @param $value
     * @param null $endpoint
     * @return self
     */
    public static function createString($key, $value, $endpoint = null)
    {
        return new self($key, $value, self::TYPE_STRING, $endpoint);
    }
}
