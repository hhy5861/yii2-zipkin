<?php

namespace mike\zipkin;

class Endpoint
{
    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var string
     */
    private $ipv4;

    /**
     * @var int|null
     */
    private $port;

    /**
     * @var string|null
     */
    private $ipv6;

    /**
     * @param $serviceName
     * @param string|null $ipv4
     * @param int|null $port
     * @param string|null $ipv6
     */
    public function __construct($serviceName, $ipv4 = null, $port = null, $ipv6 = null)
    {
        if (!$ipv4) {
            $ipv4 = Utils::systemIp();
        }

        $this->serviceName = $serviceName;
        $this->ipv4 = $ipv4;
        $this->port = $port;
        $this->ipv6 = $ipv6;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @return string
     */
    public function getIpv4()
    {
        return $this->ipv4;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return null|string
     */
    public function getIpv6()
    {
        return $this->ipv6;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [
            'serviceName' => $this->serviceName,
            'ipv4' => $this->ipv4,
        ];

        if ($this->getPort()) {
            $data['port'] = $this->getPort();
        }

        if ($this->getIpv6()) {
            $data['ipv6'] = $this->getIpv6();
        }

        return $data;
    }
}
