<?php

namespace SimpleBus\RabbitMQBundle;

use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;
use SimpleBus\RabbitMQBundle\ErrorHandling\ErrorHandler;
use SimpleBus\RabbitMQBundle\Event\Events;
use SimpleBus\RabbitMQBundle\Event\MessageConsumed;
use SimpleBus\RabbitMQBundle\Event\MessageConsumptionFailed;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RabbitMQMessageConsumer implements ConsumerInterface
{
    /**
     * @var SerializedEnvelopeConsumer
     */
    private $consumer;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(SerializedEnvelopeConsumer $consumer, EventDispatcherInterface $eventDispatcher)
    {
        $this->consumer = $consumer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(AMQPMessage $msg)
    {
        try {
            $this->consumer->consume($msg->body);

            $this->eventDispatcher->dispatch(
                Events::MESSAGE_CONSUMED,
                new MessageConsumed($msg)
            );
        } catch (Exception $exception) {
            $this->eventDispatcher->dispatch(
                Events::MESSAGE_CONSUMPTION_FAILED,
                new MessageConsumptionFailed($msg, $exception)
            );
        }
    }
}
