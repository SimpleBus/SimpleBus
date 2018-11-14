<?php

namespace SimpleBus\Asynchronous\Tests\MessageBus;

use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\MessageBus\PublishConditionalMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;

class PublishConditionalMessagesTest extends TestCase
{

    /**
     * @param $message
     * @param $expectedPublish
     *
     * @dataProvider queueMessagesDataProvider
     */
    public function testPublishesOnlySplQueueMessages($message, $expectedPublish)
    {
        $nextCallableCalled = false;
        $alwaysSucceedingNextCallable = function ($actualMessage) use ($message, &$nextCallableCalled) {
            $nextCallableCalled = true;
            $this->assertSame($message, $actualMessage);
        };

        /** @var \PHPUnit_Framework_MockObject_MockObject|Publisher $publisher */
        $publisher = $this->createMock(Publisher::class);
        $publisher
            ->expects($expectedPublish ? $this->once() : $this->never())
            ->method('publish');

        $condition = function ($message) {
            return $message instanceof \SplQueue;
        };

        $middleware = new PublishConditionalMessages($publisher, $condition);
        $middleware->handle($message, $alwaysSucceedingNextCallable);

        $this->assertTrue($nextCallableCalled);
    }


    /**
     * @param $message
     * @param $expectedPublish
     * @dataProvider insideLogicDataProvider
     */
    public function testPublishesOnlyAsyncMessages($message, $expectedPublish)
    {
        $nextCallableCalled = false;
        $alwaysSucceedingNextCallable = function ($actualMessage) use ($message, &$nextCallableCalled) {
            $nextCallableCalled = true;
            $this->assertSame($message, $actualMessage);
        };

        /** @var \PHPUnit_Framework_MockObject_MockObject|Publisher $publisher */
        $publisher = $this->createMock(Publisher::class);
        $publisher
            ->expects($expectedPublish ? $this->once() : $this->never())
            ->method('publish');

        $condition = function ($message) {
            /*
             * Here can be check like
             *
             * if ($message instanceof CanBeAsync) {
             *      return $message->isAsync();
             * }
             *
             * return false;
             *
             */
            if (\is_object($message) && method_exists($message, 'isAsync') && \is_callable([$message, 'isAsync'])) {
                return $message->isAsync();
            }

            return false;
        };

        $middleware = new PublishConditionalMessages($publisher, $condition);
        $middleware->handle($message, $alwaysSucceedingNextCallable);

        $this->assertTrue($nextCallableCalled);
    }

    /**
     * @return array
     */
    public function queueMessagesDataProvider(): array
    {
        return [
            [new \SplQueue(), true],
            [new \stdClass(), false],
        ];
    }

    public function insideLogicDataProvider(): array
    {
        $async = new class {
            public function isAsync(): bool
            {
                return true;
            }
        };

        $notAsync = new class {
            public function isAsync(): bool
            {
                return false;
            }
        };

        $another = new \stdClass();

        return [
            [$async, true],
            [$notAsync, false],
            [$another, false],
        ];
    }
}