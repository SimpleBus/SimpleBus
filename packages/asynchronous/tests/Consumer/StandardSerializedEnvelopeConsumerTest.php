<?php

namespace SimpleBus\Asynchronous\Tests\Consumer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Consumer\StandardSerializedEnvelopeConsumer;
use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer;
use stdClass;

final class StandardSerializedEnvelopeConsumerTest extends TestCase
{
    /**
     * @test
     */
    public function itDeserializesTheEnvelopAndLetsTheMessageBusHandleTheMessage(): void
    {
        $serializedEnvelope = 'serialized envelop';

        $message = $this->dummyMessage();
        $envelope = DefaultEnvelope::forMessage($message);

        $envelopeSerializer = $this->mockMessageInEnvelopeSerializer();
        $envelopeSerializer
            ->expects($this->once())
            ->method('unwrapAndDeserialize')
            ->with($serializedEnvelope)
            ->will($this->returnValue($envelope));

        $messageBus = $this->mockMessageBus();
        $messageBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->identicalTo($message));

        $consumer = new StandardSerializedEnvelopeConsumer($envelopeSerializer, $messageBus);
        $consumer->consume($serializedEnvelope);
    }

    private function dummyMessage(): stdClass
    {
        return new stdClass();
    }

    /**
     * @return MessageBus|MockObject
     */
    private function mockMessageBus()
    {
        return $this->createMock(MessageBus::class);
    }

    /**
     * @return MessageInEnvelopeSerializer|MockObject
     */
    private function mockMessageInEnvelopeSerializer()
    {
        return $this->createMock(MessageInEnvelopeSerializer::class);
    }
}
