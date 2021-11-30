<?php

namespace SimpleBus\Asynchronous\Tests\MessageBus;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\MessageBus\AlwaysPublishesMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;
use stdClass;

class AlwaysPublishesMessagesTest extends TestCase
{
    /**
     * @test
     */
    public function itPublishesAMessageAndCallsTheNextMiddleware(): void
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
}
