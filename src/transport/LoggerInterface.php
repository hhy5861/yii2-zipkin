<?php

namespace mike\zipkin\transport;

use mike\zipkin\Span;

interface LoggerInterface
{
    /**
     * @param Span[] $spans
     * @throws \Exception
     */
    public function log(array $spans);
}
