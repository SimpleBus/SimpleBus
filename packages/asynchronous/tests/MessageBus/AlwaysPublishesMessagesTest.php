<?php

namespace SimpleBus\Asynchronous\Tests\MessageBus;

use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\MessageBus\AlwaysPublishesMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;

/**
 * @internal
 * @coversNothing
 */
class AlwaysPublishesMessagesTest extends TestCase
{
    /**
     * @test
     */
    public function itPublishesAMessageAndCallsTheNextMiddleware()
    {
        $message = $this->dummyMessage();
        $publisher = $this->mockPublisher();
        $publisher
            ->expects($this->once())
            ->method('publish')
            ->with($this->identicalTo($message));

        $nextCallableCalled = false;
        $next = function ($actualMessage) use ($message, &$nextCallableCalled) {
            $nextCallableCalled = true;
            $this->assertSame($message, $actualMessage);
        };

        $middleware = new AlwaysPublishesMessages($publisher);
        $middleware->handle($message, $next);

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
     * @return object|\PHPUnit\Framework\MockObject\MockObject
     */
    private function dummyMessage()
    {
        return new \stdClass();
    }
}
