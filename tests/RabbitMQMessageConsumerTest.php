<?php

namespace SimpleBus\RabbitMQBundle\Tests;

use PhpAmqpLib\Message\AMQPMessage;
use SimpleBus\RabbitMQBundle\RabbitMQMessageConsumer;

class RabbitMQMessageConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_deserializes_the_message_and_hands_it_over_to_the_message_bus()
    {
        $messageBody = 'the-serialized-message-body';
        $deserializedMessage = $this->dummyMessage();
        $serializer = $this->mockSerializer();
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($messageBody)
            ->will($this->returnValue($deserializedMessage));

        $messageBus = $this->mockMessageBus();
        $messageBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->identicalTo($deserializedMessage));

        $consumer = new RabbitMQMessageConsumer($serializer, $messageBus);

        $consumer->execute($this->newAMQPMessage($messageBody));
    }

    private function mockSerializer()
    {
        return $this->getMock('SimpleBus\Asynchronous\Message\Serializer\MessageSerializer');
    }

    private function mockMessageBus()
    {
        return $this->getMock('SimpleBus\Message\Bus\MessageBus');
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
