<?php

namespace SimpleBus\Message\Tests\Logging;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use SimpleBus\Message\Logging\LoggingMiddleware;
use stdClass;

/**
 * @internal
 * @coversNothing
 */
class LoggingMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function itLogsMessagesBeforeAndAfterHandlingIt(): void
    {
        $orderOfEvents = [];
        $message = $this->dummyMessage();

        $logLevel = LogLevel::DEBUG;

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->exactly(2))
            ->method('log')
            ->will($this->returnCallback(function ($actualLevel, $logMessage, array $context) use (&$orderOfEvents, $message, $logLevel) {
                $orderOfEvents[] = 'Logged: '.$logMessage;
                $this->assertSame(['message' => $message], $context);
                $this->assertSame($logLevel, $actualLevel);
            }));

        $middleware = new LoggingMiddleware($logger, $logLevel);

        $next = function ($actualMessage) use (&$orderOfEvents, $message) {
            $orderOfEvents[] = 'Called next middleware';
            $this->assertSame($message, $actualMessage);
        };
        $middleware->handle($message, $next);

        $this->assertSame(
            [
                'Logged: Started handling a message',
                'Called next middleware',
                'Logged: Finished handling a message',
            ],
            $orderOfEvents
        );
    }

    private function dummyMessage(): stdClass
    {
        return new stdClass();
    }
}
