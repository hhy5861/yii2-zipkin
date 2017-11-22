<?php

namespace mike\zipkin;

class Utils
{
    /**
     * @return string
     */
    public static function id()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $id = time() . rand(100000, 999999);
        } else {
            $id = bin2hex(file_get_contents('/dev/urandom', 0, null, -0, 8));
        }

        return $id;
    }

    /**
     * @return string
     */
    public static function systemIp()
    {
        $ip = @exec('hostname -I | cut -d " " -f 1');

        return $ip ?: '127.0.0.1';
    }

    /**
     * @return int
     */
    public static function microseconds()
    {
        return intval(microtime(true) * 1000 * 1000);
    }

    /**
     * @return array
     */
    public static function getHeaders()
    {
        static $headers = [];

        if (!$headers) {
            if (function_exists('getallheaders')) {
                $headers = (array)getallheaders();
            } elseif (function_exists('http_get_request_headers')) {
                $headers = http_get_request_headers();
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (strncmp($name, 'HTTP_', 5) === 0) {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $headers[$name] = $value;
                    }
                }
            }
        }

        $headers = array_change_key_case($headers, CASE_LOWER);

        return $headers;
    }

    /**
     * @param $name
     * @return string|null
     */
    public static function getHeader($name)
    {
        $headers = self::getHeaders();
        $name = strtolower($name);

        return empty($headers[$name]) ? null : $headers[$name];
    }
}
