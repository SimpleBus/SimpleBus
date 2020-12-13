<?php

namespace SimpleBus\Asynchronous\Tests\MessageBus;

use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\MessageBus\PublishesPredefinedMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\Name\MessageNameResolver;

/**
 * @internal
 * @coversNothing
 */
class PublishesPredefinedMessagesTest extends TestCase
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
    public function itCallsTheNextMiddlewareAndWhenTheMessageHasNoHandlerItPublishesIt()
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
     * @return \PHPUnit\Framework\MockObject\MockObject|Publisher
     */
    private function mockPublisher()
    {
        return $this->createMock('SimpleBus\Asynchronous\Publisher\Publisher');
    }

    /**
     * @return MessageNameResolver|\PHPUnit\Framework\MockObject\MockObject
     */
    private function mockNameResolver()
    {
        return $this->createMock('SimpleBus\Message\Name\MessageNameResolver');
    }

    /**
     * @return object|\PHPUnit\Framework\MockObject\MockObject
     */
    private function dummyMessage()
    {
        return new \stdClass();
    }
}
