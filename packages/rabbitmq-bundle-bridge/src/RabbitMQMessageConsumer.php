<?php

namespace SimpleBus\RabbitMQBundleBridge;

use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;
use SimpleBus\RabbitMQBundleBridge\Event\Events;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumed;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumptionFailed;
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
                new MessageConsumed($msg),
                Events::MESSAGE_CONSUMED
            );

            return self::MSG_ACK;
        } catch (Exception $exception) {
            $this->eventDispatcher->dispatch(
                new MessageConsumptionFailed($msg, $exception),
                Events::MESSAGE_CONSUMPTION_FAILED
            );

            return self::MSG_REJECT;
        }
    }
}
