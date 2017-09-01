<?php

namespace mike\zipkin;

class Annotation
{
    const CLIENT_SEND = 'cs';

    const CLIENT_RECV = 'cr';

    const SERVER_SEND = 'ss';

    const SERVER_RECV = 'sr';

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var Endpoint|null
     */
    private $endpoint;

    /**
     * @param string $value
     * @param int $timestamp
     * @param Endpoint|null $endpoint
     */
    public function __construct($value, $timestamp, Endpoint $endpoint = null)
    {
        $this->value = $value;
        $this->timestamp = $timestamp;
        $this->endpoint = $endpoint;
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
    public function getTimestamp()
    {
        return $this->timestamp;
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
            'value' => $this->value,
            'timestamp' => $this->timestamp,
        ];

        if ($this->endpoint) {
            $data['endpoint'] = $this->endpoint->toArray();
        }

        return $data;
    }

    /**
     * @param $value
     * @param int|null $timestamp
     * @param Endpoint|null $endpoint
     * @return self
     */
    protected static function create($value, $timestamp = null, Endpoint $endpoint = null)
    {
        if (!$timestamp) {
            $timestamp = Utils::microseconds();
        }

        return new self($value, $timestamp, $endpoint);
    }

    /**
     * @param int|null $timestamp
     * @param Endpoint|null $endpoint
     * @return self
     */
    public static function createClientSend($timestamp = null, Endpoint $endpoint = null)
    {
        return self::create(self::CLIENT_SEND, $timestamp, $endpoint);
    }

    /**
     * @param int|null $timestamp
     * @param Endpoint|null $endpoint
     * @return self
     */
    public static function createClientRecv($timestamp = null, Endpoint $endpoint = null)
    {
        return self::create(self::CLIENT_RECV, $timestamp, $endpoint);
    }

    /**
     * @param int|null $timestamp
     * @param Endpoint|null $endpoint
     * @return self
     */
    public static function createServerSend($timestamp = null, Endpoint $endpoint = null)
    {
        return self::create(self::SERVER_SEND, $timestamp, $endpoint);
    }

    /**
     * @param int|null $timestamp
     * @param Endpoint|null $endpoint
     * @return self
     */
    public static function createServerRecv($timestamp = null, Endpoint $endpoint = null)
    {
        return self::create(self::SERVER_RECV, $timestamp, $endpoint);
    }
}
