<?php

namespace Message\Dispatcher;

use SimpleBus\Asynchronous\Message\Dispatcher\AsynchronousEventDispatcher;
use SimpleBus\Message\Message;

class AsynchronousEventDispatcherTest extends \PHPUnit_Framework_TestCase
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
        $next = function (Message $actualMessage) use ($message, &$nextCallableCalled) {
            $nextCallableCalled = true;
            $this->assertSame($message, $actualMessage);
        };

        $middleware = new AsynchronousEventDispatcher($publisher);
        $middleware->handle($message, $next);

        $this->assertTrue($nextCallableCalled);
    }

    private function mockPublisher()
    {
        return $this->getMock('SimpleBus\Asynchronous\Message\Publisher\Publisher');
    }

    private function dummyMessage()
    {
        return $this->getMock('SimpleBus\Message\Message');
    }
}
