<?php

namespace SimpleBus\RabbitMQBundle\Tests;

use Exception;
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

        $consumer = new RabbitMQMessageConsumer($serializedEnvelopeConsumer, $this->errorHandlerDummy());

        $consumer->execute($this->newAMQPMessage($serializedEnvelope));
    }

    /**
     * @test
     */
    public function it_handles_an_error_but_throws_no_exception_if_consuming_the_message_fails()
    {
        $exception = new Exception('I always fail');
        $serializedEnvelopeConsumer = $this->mockSerializedEnvelopeConsumer();
        $serializedEnvelopeConsumer
            ->expects($this->once())
            ->method('consume')
            ->will($this->throwException($exception));
        $message = $this->newAMQPMessage();

        $errorHandler = $this->errorHandlerHandles($exception, $message);

        $consumer = new RabbitMQMessageConsumer($serializedEnvelopeConsumer, $errorHandler);

        $consumer->execute($message);
    }

    private function newAMQPMessage($messageBody  = '')
    {
        return new AMQPMessage($messageBody);
    }

    private function mockSerializedEnvelopeConsumer()
    {
        return $this->getMock('SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer');
    }

    private function errorHandlerDummy()
    {
        return $this->getMock('SimpleBus\RabbitMQBundle\ErrorHandling\ErrorHandler');
    }

    private function errorHandlerHandles(Exception $exception, AMQPMessage $message)
    {
        $errorHandler = $this->getMock('SimpleBus\RabbitMQBundle\ErrorHandling\ErrorHandler');

        $errorHandler
            ->expects($this->once())
            ->method('handle')
            ->with($this->identicalTo($message), $this->identicalTo($exception));

        return $errorHandler;
    }
}
