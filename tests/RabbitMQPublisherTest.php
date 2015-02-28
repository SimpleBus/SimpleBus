<?php

namespace SimpleBus\RabbitMQBundle\Tests;

use PhpAmqpLib\Message\AMQPMessage;
use SimpleBus\RabbitMQBundle\RabbitMQPublisher;

class RabbitMQPublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_serializes_the_message_and_publishes_it()
    {
        $message = $this->dummyMessage();
        $serializedMessageEnvelope = 'the-serialized-message-envelope';
        $serializer = $this->mockSerializer();
        $serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($message)
            ->will($this->returnValue($serializedMessageEnvelope));

        $producer = $this->mockProducer();
        $producer
            ->expects($this->once())
            ->method('publish')
            ->with($this->identicalTo($serializedMessageEnvelope));

        $publisher = new RabbitMQPublisher($serializer, $producer);

        $publisher->publish($message);
    }

    private function mockSerializer()
    {
        return $this->getMock('SimpleBus\Asynchronous\Message\Serializer\MessageSerializer');
    }

    private function mockProducer()
    {
        return $this
            ->getMockBuilder('OldSound\RabbitMqBundle\RabbitMq\Producer')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function newAMQPMessage($messageBody)
    {
        return new AMQPMessage($messageBody);
    }

    private function dummyMessage()
    {
        return $this->getMock('SimpleBus\Message\Message');
    }
}
