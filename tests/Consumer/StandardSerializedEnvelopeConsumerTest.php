<?php

namespace SimpleBus\Asynchronous\Tests\Consumer;

use SimpleBus\Asynchronous\Consumer\StandardSerializedEnvelopeConsumer;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer;
use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\Message\Message;

class StandardSerializedEnvelopeConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_deserializes_the_envelop_and_lets_the_message_bus_handle_the_message()
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Message
     */
    private function dummyMessage()
    {
        return $this->getMock('SimpleBus\Message\Message');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|MessageBus
     */
    private function mockMessageBus()
    {
        return $this->getMock('SimpleBus\Message\Bus\MessageBus');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|MessageInEnvelopSerializer
     */
    private function mockMessageInEnvelopeSerializer()
    {
        return $this->getMock('SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer');
    }
}
