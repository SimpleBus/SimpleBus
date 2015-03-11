<?php

namespace SimpleBus\RabbitMQBundle\Tests\ErrorHandling;

use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use SimpleBus\RabbitMQBundle\ErrorHandling\DelegatingErrorHandler;

class DelegatingErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_delegates_to_other_error_handlers()
    {
        $exception = new Exception();
        $message = new AMQPMessage();

        $errorHandler = new DelegatingErrorHandler([
            $this->errorHandlerShouldBeCalled($message, $exception),
            $this->errorHandlerShouldBeCalled($message, $exception)
        ]);

        $errorHandler->handle($message, $exception);
    }

    private function errorHandlerShouldBeCalled(AMQPMessage $expectedMessage, Exception $expectedException)
    {
        $errorHandler = $this->getMock('SimpleBus\RabbitMQBundle\ErrorHandling\ErrorHandler');
        $errorHandler
            ->expects($this->once())
            ->method('handle')
            ->with($this->identicalTo($expectedMessage), $this->identicalTo($expectedException));

        return $errorHandler;
    }
}
