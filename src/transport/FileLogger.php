<?php

namespace mike\zipkin\transport;

use mike\zipkin\Span;

class FileLogger implements LoggerInterface
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var int
     */
    private $maxFileSize;

    /**
     * @var int
     */
    private $dirMode;

    /**
     * @param string $file
     * @param int $maxFileSize
     * @param int $dirMode
     */
    public function __construct($file, $maxFileSize = 10240, $dirMode = 0775)
    {
        $this->file = $file;
        $this->maxFileSize = $maxFileSize < 1 ? 1 : $maxFileSize;
        $this->dirMode = $dirMode;
    }

    /**
     * @inheritdoc
     */
    public function log(array $spans)
    {
        $dir = dirname($this->file);

        if (!is_dir($dir)) {
            if (@mkdir($dir, $this->dirMode, true) === false) {
                throw new \Exception("Unable to create directory: {$dir}");
            }
        }

        $text = implode(PHP_EOL, array_map(function (Span $span) {
            return json_encode($span->toArray());
        }, $spans));

        $text .= PHP_EOL;

        if (($fp = @fopen($this->file, 'a')) === false) {
            throw new \Exception("Unable to append to file: {$this->file}");
        }

        @flock($fp, LOCK_EX);
        clearstatcache();

        if (@filesize($this->file) > $this->maxFileSize * 1024) {
            $rotateFile = $this->file . '.' . date('YmdHis') . '.' . rand(100000, 999999);
            @rename($this->file, $rotateFile);
            @flock($fp, LOCK_UN);
            @fclose($fp);
            @file_put_contents($this->file, $text, FILE_APPEND | LOCK_EX);
        } else {
            @fwrite($fp, $text);
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
    }
}
