<?php

namespace SimpleBus\Asynchronous\Tests\MessageBus;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use SimpleBus\Asynchronous\MessageBus\PublishesUnhandledMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\CallableResolver\Exception\UndefinedCallable;

class PublishesUnhandledMessagesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_calls_the_next_middleware_and_when_the_message_is_handled_it_does_not_publish_it()
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
    public function it_calls_the_next_middleware_and_when_the_message_has_no_handler_it_publishes_it()
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Publisher
     */
    private function mockPublisher()
    {
        return $this->createMock('SimpleBus\Asynchronous\Publisher\Publisher');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|object
     */
    private function dummyMessage()
    {
        return new \stdClass();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private function dummyLogger()
    {
        return $this->createMock('Psr\Log\LoggerInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
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
