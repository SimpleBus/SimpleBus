<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use Psr\Log\AbstractLogger;
use RuntimeException;

final class FileLogger extends AbstractLogger
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function clearFile(): void
    {
        file_put_contents($this->path, '');
    }

    /**
     * @param array<mixed> $context
     * @param mixed        $level
     * @param mixed        $message
     */
    public function log($level, $message, array $context = []): void
    {
        $line = sprintf("%s %s %s\n", $level, $message, json_encode($context));

        file_put_contents($this->path, $line, FILE_APPEND);
    }

    public function fileContains(string $text): void
    {
        $fileContents = $this->fileContents();

        if (false !== strpos($fileContents, $text)) {
            return;
        }

        throw new RuntimeException(sprintf('The `%s` file does not contains text `%s` it has `%s`', $this->path, $text, $fileContents));
    }

    public function fileContents(): string
    {
        if (false === $content = file_get_contents($this->path)) {
            throw new RuntimeException(sprintf('Can\'t read the `%s` file', $this->path));
        }

        return $content;
    }
}
