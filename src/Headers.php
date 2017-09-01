<?php

namespace mike\zipkin;

class Headers
{
    /**
     * @var string|null
     */
    private $traceId;

    /**
     * @var string|null
     */
    private $spanId;

    /**
     * @var string|null
     */
    private $parentSpanId;

    /**
     * @var bool|null
     */
    private $sampled;

    /**
     * @var int|null
     */
    private $flags;

    /**
     * @param string|null $traceId
     * @param string|null $spanId
     * @param string|null $parentSpanId
     * @param bool $sampled
     * @param int|null $flags
     */
    public function __construct($traceId = null, $spanId = null, $parentSpanId = null, $sampled = true, $flags = null)
    {
        $this->traceId = $traceId;
        $this->spanId = $spanId;
        $this->parentSpanId = $parentSpanId;
        $this->sampled = $sampled;
        $this->flags = $flags;
    }

    /**
     * @return null|string
     */
    public function getTraceId()
    {
        return $this->traceId;
    }

    /**
     * @return null|string
     */
    public function getSpanId()
    {
        return $this->spanId;
    }

    /**
     * @return null|string
     */
    public function getParentSpanId()
    {
        return $this->parentSpanId;
    }

    /**
     * @return bool|null
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
     * @return array
     */
    public function toArray()
    {
        return [
            'X-B3-TraceId' => $this->getTraceId(),
            'X-B3-SpanId' => $this->getSpanId(),
            'X-B3-ParentSpanId' => $this->getParentSpanId(),
            'X-B3-Sampled' => $this->getSampled() ? 1 : 0,
            'X-B3-Flags' => $this->getFlags(),
        ];
    }

    /**
     * @return self
     */
    public static function createFromHttp()
    {
        return new self(
            Utils::getHeader('X-B3-TraceId'),
            Utils::getHeader('X-B3-SpanId'),
            Utils::getHeader('X-B3-ParentSpanId'),
            Utils::getHeader('X-B3-Sampled'),
            Utils::getHeader('X-B3-Flags')
        );
    }

    /**
     * @param Span $span
     * @return self
     */
    public static function createFromSapn(Span $span)
    {
        return new self(
            $span->getTracer()->getTraceId(),
            $span->getSpanId(),
            $span->getParentSpanId(),
            $span->getTracer()->getSampled(),
            $span->getTracer()->getFlags()
        );
    }
}
