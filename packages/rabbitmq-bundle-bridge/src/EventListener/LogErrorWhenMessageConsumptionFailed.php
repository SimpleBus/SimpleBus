<?php

namespace SimpleBus\RabbitMQBundleBridge\EventListener;

use Psr\Log\LoggerInterface;
use SimpleBus\RabbitMQBundleBridge\Event\Events;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumptionFailed;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogErrorWhenMessageConsumptionFailed implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    private string $logLevel;

    private string $logMessage;

    public function __construct(LoggerInterface $logger, string $logLevel, string $logMessage)
    {
        $this->logger = $logger;
        $this->logLevel = $logLevel;
        $this->logMessage = $logMessage;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::MESSAGE_CONSUMPTION_FAILED => 'messageConsumptionFailed',
        ];
    }

    /**
     * Log the failed message and the related exception.
     */
    public function messageConsumptionFailed(MessageConsumptionFailed $event): void
    {
        $this->logger->log(
            $this->logLevel,
            $this->logMessage,
            [
                'exception' => $event->exception(),
                'message' => $event->message(),
            ]
        );
    }
}
