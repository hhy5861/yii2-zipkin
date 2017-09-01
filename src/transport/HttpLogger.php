<?php

namespace mike\zipkin\transport;

use mike\zipkin\Span;

class HttpLogger implements LoggerInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @param string $url
     * @param int $timeout
     */
    public function __construct($url = 'http://localhost:9411/api/v1/spans', $timeout = 3)
    {
        $this->url = $url;
        $this->timeout = $timeout;
    }

    /**
     * @inheritdoc
     */
    public function log(array $spans)
    {
        $data = json_encode(array_map(function (Span $span) {
            return $span->toArray();
        }, $spans));

        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);

        $result = curl_exec($ch);

        $code = null;
        $error = null;

        if (0 != ($errno = curl_errno($ch))) {
            $code = $errno;
            $error = curl_error($ch);
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (strpos((string)$httpCode, '2') !== 0) {
                $code = $httpCode;
                $error = $result;
            }
        }

        if ($code) {
            throw new \Exception("Failed to log: {$error}", $code);
        }
    }
}
