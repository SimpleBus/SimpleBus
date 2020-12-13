<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\ErrorHandling;

use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumptionFailed;
use SimpleBus\RabbitMQBundleBridge\EventListener\LogErrorWhenMessageConsumptionFailed;

/**
 * @internal
 * @coversNothing
 */
class LogErrorWhenMessageConsumptionFailedTest extends TestCase
{
    /**
     * @test
     */
    public function itLogsTheError()
    {
        $exception = new Exception();
        $message = new AMQPMessage();

        $logLevel = LogLevel::CRITICAL;
        $logMessage = 'Failed to handle a message';

        $logger = $this->loggerShouldLog($logLevel, $logMessage, ['message' => $message, 'exception' => $exception]);

        $errorHandler = new LogErrorWhenMessageConsumptionFailed($logger, $logLevel, $logMessage);

        $errorHandler->messageConsumptionFailed(new MessageConsumptionFailed($message, $exception));
    }

    private function loggerShouldLog($logLevel, $logMessage, $context)
    {
        $logger = $this->createMock('Psr\Log\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('log')
            ->with($logLevel, $logMessage, $context);

        return $logger;
    }
}
