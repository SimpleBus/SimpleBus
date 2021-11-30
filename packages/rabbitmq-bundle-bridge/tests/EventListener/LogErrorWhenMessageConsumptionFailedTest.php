<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\ErrorHandling;

use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumptionFailed;
use SimpleBus\RabbitMQBundleBridge\EventListener\LogErrorWhenMessageConsumptionFailed;

class LogErrorWhenMessageConsumptionFailedTest extends TestCase
{
    /**
     * @test
     */
    public function itLogsTheError(): void
    {
        $exception = new Exception();
        $message = new AMQPMessage();

        $logLevel = LogLevel::CRITICAL;
        $logMessage = 'Failed to handle a message';

        $logger = $this->loggerShouldLog($logLevel, $logMessage, ['message' => $message, 'exception' => $exception]);

        $errorHandler = new LogErrorWhenMessageConsumptionFailed($logger, $logLevel, $logMessage);

        $errorHandler->messageConsumptionFailed(new MessageConsumptionFailed($message, $exception));
    }

    /**
     * @param mixed[] $context
     *
     * @return LoggerInterface|MockObject
     */
    private function loggerShouldLog(string $logLevel, string $logMessage, array $context)
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('log')
            ->with($logLevel, $logMessage, $context);

        return $logger;
    }
}
