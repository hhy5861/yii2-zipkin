<?php

require __DIR__ . '/../vendor/autoload.php';

use mike\zipkin\Tracer;
use mike\zipkin\transport\HttpLogger;
use mike\zipkin\transport\FileLogger;
use mike\zipkin\Headers;

$headers = Headers::createFromHttp();

$httpLogger = new HttpLogger();
$fileLogger = new FileLogger('/tmp/zipkin.trace.log');
$tracer = new Tracer('web', $headers->getTraceId());
$tracer->setLogger($httpLogger)->setLogger($fileLogger);

$span1 = $tracer->createSpan('request', $headers->getSpanId());

$span1->start()->serverRecv();

$span2 = $tracer->createSpan('func1', $span1->getSpanId());

$span2->start();

$span2->finish();

$span2->addBinaryAnnotation('age', '20');

$span3 = $tracer->createSpan('func2', $span1->getSpanId())->start();

$span3->finish();

$span1->serverSend()->finish();

$tracer->flush();

echo $tracer->getTraceId();

$headers2 = Headers::createFromSapn($span2);
var_dump($headers2->toArray());
