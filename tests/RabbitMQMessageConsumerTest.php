<?php

namespace SimpleBus\RabbitMQBundle\Tests;

use PhpAmqpLib\Message\AMQPMessage;
use SimpleBus\RabbitMQBundle\RabbitMQMessageConsumer;

class RabbitMQMessageConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_consumes_the_message_body_as_a_serialized_envelope()
    {
        $serializedEnvelope = 'a serialized envelope';
        $serializedEnvelopeConsumer = $this->mockSerializedEnvelopeConsumer();
        $serializedEnvelopeConsumer
            ->expects($this->once())
            ->method('consume')
            ->with($serializedEnvelope);

        $consumer = new RabbitMQMessageConsumer($serializedEnvelopeConsumer);

        $consumer->execute($this->newAMQPMessage($serializedEnvelope));
    }

    private function newAMQPMessage($messageBody)
    {
        return new AMQPMessage($messageBody);
    }

    private function mockSerializedEnvelopeConsumer()
    {
        return $this->getMock('SimpleBus\Asynchronous\Message\Envelope\Consumer\SerializedEnvelopeConsumer');
    }
}
