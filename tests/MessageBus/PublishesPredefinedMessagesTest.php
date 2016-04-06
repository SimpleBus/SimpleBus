<?php

namespace SimpleBus\Asynchronous\Tests\MessageBus;

use SimpleBus\Asynchronous\MessageBus\PublishesPredefinedMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\CallableResolver\Exception\UndefinedCallable;
use SimpleBus\Message\Name\MessageNameResolver;

class PublishesPredefinedMessagesTest extends \PHPUnit_Framework_TestCase
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

        $nameResolver = $this->mockNameResolver();
        $nameResolver->method('resolve')
            ->willReturn('foo');

        $middleware = new PublishesPredefinedMessages($publisher, $nameResolver, ['baz', 'bar']);

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
        };

        $publisher = $this->mockPublisher();
        $publisher
            ->expects($this->once())
            ->method('publish')
            ->with($this->identicalTo($message));

        $nameResolver = $this->mockNameResolver();
        $nameResolver->method('resolve')
            ->willReturn('foo');

        $middleware = new PublishesPredefinedMessages($publisher, $nameResolver, ['baz', 'foo', 'bar']);

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
     * @return \PHPUnit_Framework_MockObject_MockObject|MessageNameResolver
     */
    private function mockNameResolver()
    {
        return $this->getMock('SimpleBus\Message\Name\MessageNameResolver');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|object
     */
    private function dummyMessage()
    {
        return new \stdClass();
    }
}
