<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use Psr\Log\AbstractLogger;
use RuntimeException;

class FileLogger extends AbstractLogger
{
    /**
     * @var string
     */
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function clearFile()
    {
        file_put_contents($this->path, '');
    }

    public function log($level, $message, array $context = array())
    {
        $line = sprintf("%s %s %s\n", $level, $message, json_encode($context));

        file_put_contents($this->path, $line, FILE_APPEND);
    }

    public function fileContains($text)
    {
        $fileContents = $this->fileContents();

        if (strpos($fileContents, $text) !== false) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'The `%s` file does not contains text `%s` it has `%s`',
                $this->path,
                $text,
                $fileContents
            )
        );
    }

    public function fileContents()
    {
        return file_get_contents($this->path);
    }
}
