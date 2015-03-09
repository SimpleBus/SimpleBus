<?php

namespace SimpleBus\Asynchronous\Tests\MessageBus;

use Psr\Log\LoggerInterface;
use SimpleBus\Asynchronous\MessageBus\PublishesUnhandledMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\Handler\Map\Exception\NoHandlerForMessageName;
use SimpleBus\Message\Message;

class PublishesUnhandledMessagesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_calls_the_next_middleware_and_when_the_message_is_handled_it_does_not_publish_it()
    {
        $message = $this->dummyMessage();

        $nextCallableCalled = false;
        $alwaysSucceedingNextCallable = function(Message $actualMessage) use ($message, &$nextCallableCalled) {
            $nextCallableCalled = true;
            $this->assertSame($message, $actualMessage);
        };

        $publisher = $this->mockPublisher();
        $publisher
            ->expects($this->never())
            ->method('publish');

        $middleware = new PublishesUnhandledMessages($publisher, $this->dummyLogger());

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
        $alwaysSucceedingNextCallable = function(Message $actualMessage) use ($message, &$nextCallableCalled) {
            $nextCallableCalled = true;
            $this->assertSame($message, $actualMessage);

            throw new NoHandlerForMessageName('message name');
        };

        $publisher = $this->mockPublisher();
        $publisher
            ->expects($this->once())
            ->method('publish')
            ->with($this->identicalTo($message));

        $logger = $this->mockLogger();
        $logger
            ->expects($this->once())
            ->method('debug')
            ->with('No message handler found, trying to handle it asynchronously', ['type' => get_class($message)]);

        $middleware = new PublishesUnhandledMessages($publisher, $logger);

        $middleware->handle($message, $alwaysSucceedingNextCallable);

        $this->assertTrue($nextCallableCalled);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Publisher
     */
    private function mockPublisher()
    {
        return $this->getMock('SimpleBus\Asynchronous\Publisher\Publisher');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Message
     */
    private function dummyMessage()
    {
        return $this->getMock('SimpleBus\Message\Message');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private function dummyLogger()
    {
        return $this->getMock('Psr\Log\LoggerInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private function mockLogger()
    {
        return $this->getMock('Psr\Log\LoggerInterface');
    }
}
