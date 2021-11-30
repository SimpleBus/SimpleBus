<?php

namespace SimpleBus\Asynchronous\Tests\MessageBus;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use SimpleBus\Asynchronous\MessageBus\PublishesUnhandledMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\CallableResolver\Exception\UndefinedCallable;
use stdClass;

class PublishesUnhandledMessagesTest extends TestCase
{
    /**
     * @test
     */
    public function itCallsTheNextMiddlewareAndWhenTheMessageIsHandledItDoesNotPublishIt(): void
    {
        $message = $this->dummyMessage();

        $nextCallableCalled = false;
        $alwaysSucceedingNextCallable = function ($actualMessage) use ($message, &$nextCallableCalled) {
            $nextCallableCalled = true;
            $this->assertSame($message, $actualMessage);
        };

        $publisher = $this->mockPublisher();
        $publisher
            ->expects($this->never())
            ->method('publish');

        $middleware = new PublishesUnhandledMessages($publisher, $this->dummyLogger(), $this->dummyLogLevel());

        $middleware->handle($message, $alwaysSucceedingNextCallable);

        $this->assertTrue($nextCallableCalled);
    }

    /**
     * @test
     */
    public function itCallsTheNextMiddlewareAndWhenTheMessageHasNoHandlerItPublishesIt(): void
    {
        $message = $this->dummyMessage();

        $nextCallableCalled = false;
        $alwaysSucceedingNextCallable = function ($actualMessage) use ($message, &$nextCallableCalled) {
            $nextCallableCalled = true;
            $this->assertSame($message, $actualMessage);

            throw new UndefinedCallable('message name');
        };

        $publisher = $this->mockPublisher();
        $publisher
            ->expects($this->once())
            ->method('publish')
            ->with($this->identicalTo($message));

        $logLevel = LogLevel::DEBUG;
        $logger = $this->mockLogger();
        $logger
            ->expects($this->once())
            ->method('log')
            ->with(
                $logLevel,
                'No message handler found, trying to handle it asynchronously',
                ['type' => get_class($message)]
            );

        $middleware = new PublishesUnhandledMessages($publisher, $logger, $logLevel);

        $middleware->handle($message, $alwaysSucceedingNextCallable);

        $this->assertTrue($nextCallableCalled);
    }

    /**
     * @return MockObject|Publisher
     */
    private function mockPublisher()
    {
        return $this->createMock(Publisher::class);
    }

    private function dummyMessage(): stdClass
    {
        return new stdClass();
    }

    /**
     * @return LoggerInterface|MockObject
     */
    private function dummyLogger()
    {
        return $this->createMock(LoggerInterface::class);
    }

    /**
     * @return LoggerInterface|MockObject
     */
    private function mockLogger()
    {
        return $this->createMock(LoggerInterface::class);
    }

    private function dummyLogLevel(): string
    {
        return LogLevel::DEBUG;
    }
}
