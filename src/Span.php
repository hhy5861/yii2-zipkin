<?php

namespace mike\zipkin;

class Span
{
    /**
     * @var Tracer
     */
    private $tracer;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $spanId;

    /**
     * @var string
     */
    private $parentSpanId;

    /**
     * @var Annotation[]
     */
    private $annotations = [];

    /**
     * @var BinaryAnnotation[]
     */
    private $binaryAnnotations = [];

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var int
     */
    private $duration;

    /**
     * Span constructor.
     *
     * @param Tracer $tracer
     * @param $name
     * @param string|null $parentSpanId
     */
    public function __construct(Tracer $tracer, $name, $parentSpanId = null)
    {
        $this->tracer = $tracer;
        $this->name = $name;
        $this->spanId = Utils::id();
        $this->parentSpanId = $parentSpanId;
    }

    /**
     * @return Tracer
     */
    public function getTracer()
    {
        return $this->tracer;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
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
     * @return Annotation[]
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * @return BinaryAnnotation[]
     */
    public function getBinaryAnnotation()
    {
        return $this->binaryAnnotations;
    }

    /**
     * @return int|null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param null $timestamp
     * @return $this
     */
    public function start($timestamp = null)
    {
        if (!$timestamp) {
            $timestamp = Utils::microseconds();
        }

        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @param null $timestamp
     * @return $this
     */
    public function finish($timestamp = null)
    {
        if (!$timestamp) {
            $timestamp = Utils::microseconds();
        }

        $this->duration = round($timestamp - $this->timestamp);

        $this->tracer->record($this);

        return $this;
    }

    /**
     * @param Annotation $annotation
     * @return $this
     */
    public function addAnnotation(Annotation $annotation)
    {
        $this->annotations[] = $annotation;

        return $this;
    }

    /**
     * @param int|null $timestamp
     * @param Endpoint|null $endpoint
     * @return Span
     */
    public function clientSend($timestamp = null, Endpoint $endpoint = null)
    {
        $endpoint = $endpoint ?: $this->tracer->getEndpoint();

        return $this->addAnnotation(Annotation::createClientSend($timestamp, $endpoint));
    }

    /**
     * @param int|null $timestamp
     * @param Endpoint|null $endpoint
     * @return Span
     */
    public function clientRecv($timestamp = null, Endpoint $endpoint = null)
    {
        $endpoint = $endpoint ?: $this->tracer->getEndpoint();

        return $this->addAnnotation(Annotation::createClientRecv($timestamp, $endpoint));
    }

    /**
     * @param int|null $timestamp
     * @param Endpoint|null $endpoint
     * @return Span
     */
    public function serverSend($timestamp = null, Endpoint $endpoint = null)
    {
        $endpoint = $endpoint ?: $this->tracer->getEndpoint();

        return $this->addAnnotation(Annotation::createServerSend($timestamp, $endpoint));
    }

    /**
     * @param int|null $timestamp
     * @param Endpoint|null $endpoint
     * @return Span
     */
    public function serverRecv($timestamp = null, Endpoint $endpoint = null)
    {
        $endpoint = $endpoint ?: $this->tracer->getEndpoint();

        return $this->addAnnotation(Annotation::createServerRecv($timestamp, $endpoint));
    }

    /**
     * @param string $key
     * @param string $value
     * @param Endpoint|null $endpoint
     */
    public function addBinaryAnnotation($key, $value, Endpoint $endpoint = null)
    {
        $endpoint = $endpoint ?: $this->tracer->getEndpoint();

        $this->binaryAnnotations[] = BinaryAnnotation::createString($key, $value, $endpoint);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if ($this->parentSpanId) {
            $parentId = (string)$this->parentSpanId;
        } else {
            $parentId = null;
        }

        return [
            'id' => $this->getSpanId(),
            'name' => $this->getName(),
            'traceId' => $this->tracer->getTraceId(),
            'parentId' => $parentId,
            'timestamp' => $this->getTimestamp(),
            'duration' => $this->getDuration(),
            'debug' => $this->tracer->getFlags() == 1,
            'annotations' => array_map(function (Annotation $annotation) {
                return $annotation->toArray();
            }, $this->getAnnotations()),
            'binaryAnnotations' => array_map(function (BinaryAnnotation $binaryAnnotation) {
                return $binaryAnnotation->toArray();
            }, $this->getBinaryAnnotation()),
        ];
    }
}
