<?php

namespace SimpleBus\RabbitMQBundle\Tests\ErrorHandling;

use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LogLevel;
use SimpleBus\RabbitMQBundle\ErrorHandling\LoggingErrorHandler;

class LoggingErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_logs_the_error()
    {
        $exception = new Exception();
        $message = new AMQPMessage();

        $logLevel = LogLevel::CRITICAL;
        $logMessage = 'Failed to handle a message';

        $logger = $this->loggerShouldLog($logLevel, $logMessage, ['message' => $message, 'exception' => $exception]);

        $errorHandler = new LoggingErrorHandler($logger, $logLevel, $logMessage);

        $errorHandler->handle($message, $exception);
    }

    private function loggerShouldLog($logLevel, $logMessage, $context)
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('log')
            ->with($logLevel, $logMessage, $context);

        return $logger;
    }
}
