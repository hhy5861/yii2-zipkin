<?php

namespace mike\zipkin;

use mike\zipkin\transport\LoggerInterface;

class Tracer
{
    /**
     * @var string
     */
    public $traceId;

    /**
     * @var bool
     */
    private $sampled;

    /**
     * @var int|null
     */
    private $flags;

    /**
     * @var Endpoint
     */
    private $endpoint;

    /**
     * @var LoggerInterface[]
     */
    private $loggers;

    /**
     * @var Span
     */
    private $lastSpan;

    /**
     * @var Span[]
     */
    private $finishedSpans = [];

    /**
     * @param string $serviceName
     * @param string|null $traceId
     * @param bool $sampled
     * @param int|null $flags
     */
    public function __construct($serviceName, $traceId = null, $sampled = true, $flags = null)
    {
        if (!$traceId) {
            $traceId = Utils::id();
        }

        $this->endpoint = new Endpoint($serviceName);
        $this->traceId = $traceId;
        $this->sampled = (bool)$sampled;
        $this->flags = $flags;
    }

    /**
     * @param LoggerInterface $logger
     * @param string|null $name
     * @return $this
     */
    public function setLogger(LoggerInterface $logger, $name = null)
    {
        $name = $name ?: get_class($logger);
        $this->loggers[$name] = $logger;

        return $this;
    }

    /**
     * @return LoggerInterface[]
     */
    public function getLoggers()
    {
        return $this->loggers;
    }

    /**
     * @param $name
     * @param string|null $parentSpanId
     * @return Span
     */
    public function createSpan($name, $parentSpanId = null)
    {
        if (!$parentSpanId) {
            if ($this->getLastSpan()) {
                $parentSpanId = $this->getLastSpan()->getSpanId();
            }
        }

        return $this->lastSpan = new Span($this, $name, $parentSpanId);
    }

    /**
     * @return Span
     */
    public function getLastSpan()
    {
        return $this->lastSpan;
    }

    /**
     * @return Span[]
     */
    public function getFinishedSpans()
    {
        return $this->finishedSpans;
    }

    /**
     * @param Span $span
     */
    public function record(Span $span)
    {
        $this->finishedSpans[] = $span;
    }

    /**
     * flush
     */
    public function flush()
    {
        if ($this->getLoggers() && $this->getFinishedSpans() && ($this->sampled || $this->flags == 1)) {
            foreach ($this->getLoggers() as $logger) {
                register_shutdown_function([$logger, 'log'], $this->getFinishedSpans());
            }
        }
    }

    /**
     * @return string
     */
    public function getTraceId()
    {
        return $this->traceId;
    }

    /**
     * @return bool
     */
    public function getSampled()
    {
        return $this->sampled;
    }

    /**
     * @return int|null
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * @return Endpoint
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
