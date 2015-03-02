<?php

namespace SimpleBus\Asynchronous\Tests\Message\Consumer;

use SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelope;
use SimpleBus\Asynchronous\Tests\Message\Consumer\Fixtures\DummyConsumer;

class AbstractConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_deserializes_the_envelop_and_lets_the_message_bus_handle_the_message()
    {
        $serializedEnvelope = 'serialized envelop';

        $message = $this->getMock('SimpleBus\Message\Message');
        $envelope = DefaultEnvelope::forMessage($message);

        $envelopeSerializer = $this->getMock('SimpleBus\Asynchronous\Message\Envelope\Serializer\EnvelopeSerializer');
        $envelopeSerializer
            ->expects($this->once())
            ->method('unwrapAndDeserialize')
            ->with($serializedEnvelope)
            ->will($this->returnValue($envelope));

        $messageBus = $this->getMock('SimpleBus\Message\Bus\MessageBus');
        $messageBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->identicalTo($message));

        $consumer = new DummyConsumer($envelopeSerializer, $messageBus);
        $consumer->publicConsume($serializedEnvelope);
    }
}
