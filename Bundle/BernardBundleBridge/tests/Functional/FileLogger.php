<?php

namespace SimpleBus\BernardBundleBridge\Tests\Functional;

use Psr\Log\AbstractLogger;

/**
 * Copied from https://github.com/SimpleBus/RabbitMQBundleBridge/blob/master/tests/Functional/FileLogger.php.
 */
class FileLogger extends AbstractLogger
{
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

        return strpos($fileContents, $text) !== false;
    }

    public function fileContents()
    {
        return file_get_contents($this->path);
    }
}
