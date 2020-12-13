<?php

namespace SimpleBus\Asynchronous\Tests\MessageBus;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use SimpleBus\Asynchronous\MessageBus\PublishesUnhandledMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\CallableResolver\Exception\UndefinedCallable;

class PublishesUnhandledMessagesTest extends TestCase
{
    /**
     * @test
     */
    public function itCallsTheNextMiddlewareAndWhenTheMessageIsHandledItDoesNotPublishIt()
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
    public function itCallsTheNextMiddlewareAndWhenTheMessageHasNoHandlerItPublishesIt()
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
     * @return \PHPUnit\Framework\MockObject\MockObject|Publisher
     */
    private function mockPublisher()
    {
        return $this->createMock('SimpleBus\Asynchronous\Publisher\Publisher');
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|object
     */
    private function dummyMessage()
    {
        return new \stdClass();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    private function dummyLogger()
    {
        return $this->createMock('Psr\Log\LoggerInterface');
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    private function mockLogger()
    {
        return $this->createMock('Psr\Log\LoggerInterface');
    }

    private function dummyLogLevel()
    {
        return LogLevel::DEBUG;
    }
}
