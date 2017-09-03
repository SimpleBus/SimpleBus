<?php

namespace SimpleBus\Asynchronous\Tests\MessageBus;

use SimpleBus\Asynchronous\MessageBus\AlwaysPublishesMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;

class AlwaysPublishesMessagesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_publishes_a_message_and_calls_the_next_middleware()
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
}
