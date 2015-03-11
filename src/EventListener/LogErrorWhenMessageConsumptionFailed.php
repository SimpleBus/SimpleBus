<?php

namespace SimpleBus\RabbitMQBundle\EventListener;

use Psr\Log\LoggerInterface;
use SimpleBus\RabbitMQBundle\Event\Events;
use SimpleBus\RabbitMQBundle\Event\MessageConsumptionFailed;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogErrorWhenMessageConsumptionFailed implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $logLevel;

    /**
     * @var string
     */
    private $logMessage;

    public function __construct(LoggerInterface $logger, $logLevel, $logMessage)
    {
        $this->logger = $logger;
        $this->logLevel = $logLevel;
        $this->logMessage = $logMessage;
    }

    public static function getSubscribedEvents()
    {
        return [Events::MESSAGE_CONSUMPTION_FAILED => 'messageConsumptionFailed'];
    }

    /**
     * Log the failed message and the related exception
     */
    public function messageConsumptionFailed(MessageConsumptionFailed $event)
    {
        $this->logger->log(
            $this->logLevel,
            $this->logMessage,
            [
                'exception' => $event->exception(),
                'message' => $event->message()
            ]
        );
    }
}
