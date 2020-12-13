<?php

namespace SimpleBus\Asynchronous\Tests\Consumer;

use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Consumer\StandardSerializedEnvelopeConsumer;
use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer;

class StandardSerializedEnvelopeConsumerTest extends TestCase
{
    /**
     * @test
     */
    public function itDeserializesTheEnvelopAndLetsTheMessageBusHandleTheMessage()
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

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|object
     */
    private function dummyMessage()
    {
        return new \stdClass();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|MessageBus
     */
    private function mockMessageBus()
    {
        return $this->createMock('SimpleBus\Message\Bus\MessageBus');
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|MessageInEnvelopeSerializer
     */
    private function mockMessageInEnvelopeSerializer()
    {
        return $this->createMock('SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer');
    }
}
