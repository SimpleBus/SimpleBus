<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests;

use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;
use SimpleBus\RabbitMQBundleBridge\Event\Events;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumed;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumptionFailed;
use SimpleBus\RabbitMQBundleBridge\RabbitMQMessageConsumer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RabbitMQMessageConsumerTest extends TestCase
{
    /**
     * @test
     */
    public function itConsumesTheMessageBodyAsASerializedEnvelope(): void
    {
        $serializedEnvelope = 'a serialized envelope';
        $message = $this->newAMQPMessage($serializedEnvelope);
        $serializedEnvelopeConsumer = $this->mockSerializedEnvelopeConsumer();
        $serializedEnvelopeConsumer
            ->expects($this->once())
            ->method('consume')
            ->with($serializedEnvelope);

        $eventDispatcher = $this->eventDispatcherDispatchesMessageConsumedEvent($message);
        $consumer = new RabbitMQMessageConsumer($serializedEnvelopeConsumer, $eventDispatcher);

        $result = $consumer->execute($message);
        $this->assertSame(ConsumerInterface::MSG_ACK, $result);
    }

    /**
     * @test
     */
    public function itHandlesAnErrorButThrowsNoExceptionIfConsumingTheMessageFails(): void
    {
        $exception = new Exception('I always fail');
        $serializedEnvelopeConsumer = $this->mockSerializedEnvelopeConsumer();
        $serializedEnvelopeConsumer
            ->expects($this->once())
            ->method('consume')
            ->will($this->throwException($exception));
        $message = $this->newAMQPMessage();

        $eventDispatcher = $this->eventDispatcherDispatchesConsumptionFailedEvent($message, $exception);

        $consumer = new RabbitMQMessageConsumer($serializedEnvelopeConsumer, $eventDispatcher);

        $result = $consumer->execute($message);
        $this->assertSame(ConsumerInterface::MSG_REJECT, $result);
    }

    private function newAMQPMessage(string $messageBody = ''): AMQPMessage
    {
        return new AMQPMessage($messageBody);
    }

    /**
     * @return MockObject|SerializedEnvelopeConsumer
     */
    private function mockSerializedEnvelopeConsumer()
    {
        return $this->createMock(SerializedEnvelopeConsumer::class);
    }

    /**
     * @return EventDispatcherInterface|MockObject
     */
    private function eventDispatcherDispatchesConsumptionFailedEvent(AMQPMessage $message, Exception $exception)
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (MessageConsumptionFailed $event, string $name) use ($message, $exception) {
                $this->assertSame(Events::MESSAGE_CONSUMPTION_FAILED, $name);
                $this->assertSame($message, $event->message());
                $this->assertSame($exception, $event->exception());

                return $event;
            });

        return $eventDispatcher;
    }

    /**
     * @return EventDispatcherInterface|MockObject
     */
    private function eventDispatcherDispatchesMessageConsumedEvent(object $message)
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (MessageConsumed $event, string $name) use ($message) {
                $this->assertSame(Events::MESSAGE_CONSUMED, $name);
                $this->assertSame($message, $event->message());

                return $event;
            });

        return $eventDispatcher;
    }
}
