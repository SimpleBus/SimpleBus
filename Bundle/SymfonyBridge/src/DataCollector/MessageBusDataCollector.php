<?php

namespace SimpleBus\SymfonyBridge\DataCollector;

use SimpleBus\Message\Name\NamedMessage;
use SimpleBus\SymfonyBridge\Bus\Middleware\MessageLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class MessageBusDataCollector extends DataCollector
{
    /**
     * @var MessageLogger
     */
    private $commandLogger;

    /**
     * @var MessageLogger
     */
    private $eventLogger;

    public function __construct(MessageLogger $commandLogger = null, MessageLogger $eventLogger = null)
    {
        $this->commandLogger = $commandLogger;
        $this->eventLogger = $eventLogger;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = [
            'commands' => $this->getMessageInfo($this->commandLogger ? $this->commandLogger->getLogs() : []),
            'events' => $this->getMessageInfo($this->eventLogger ? $this->eventLogger->getLogs() : []),
        ];
    }

    public function getName()
    {
        return 'simple_bus';
    }

    public function reset()
    {
        $this->data = [
            'commands' => [],
            'events' => [],
        ];
    }

    public function hasItems(): bool
    {
        if (count($this->getCommands()) > 0) {
            return true;
        }

        if (count($this->getEvents()) > 0) {
            return true;
        }

        return false;
    }

    public function getCommands(): array
    {
        return $this->data['commands'];
    }

    public function getEvents(): array
    {
        return $this->data['events'];
    }

    /**
     * @param LogEntry[] $logEntries
     *
     * @return array
     */
    private function getMessageInfo(array $logEntries): array
    {
        return array_map(function(LogEntry $logEntry) {
            $message = $logEntry->getMessage();

            return [
                'messageClass' => $message instanceOf NamedMessage ? $message->messageName() : get_class($message),
                'timestamp' => $logEntry->getTimestamp(),
            ];
        }, $logEntries);
    }
}
