<?php

namespace SimpleBus\SymfonyBridge\DataCollector;

class LogEntry
{
    /**
     * @var object
     */
    private $message;

    /**
     * @var \DateTimeImmutable
     */
    private $timestamp;

    public function __construct($message)
    {
        $this->message = $message;
        $this->timestamp = new \DateTimeImmutable('now');
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
