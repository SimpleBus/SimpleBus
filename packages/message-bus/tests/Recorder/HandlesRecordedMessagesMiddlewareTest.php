<?php

namespace SimpleBus\Message\Tests\Recorder;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\HandlesRecordedMessagesMiddleware;
use SimpleBus\Message\Tests\Fixtures\CallableSpy;
use stdClass;

class HandlesRecordedMessagesMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function itHandlesRecordedMessages(): void
    {
        $messages = [$this->dummyMessage(), $this->dummyMessage()];
        $messageRecorder = $this->mockMessageRecorder();

        // first recorded messages should be fetched
        $messageRecorder
            ->expects($this->once())
            ->method('recordedMessages')
            ->will($this->returnValue($messages));

        // then immediately erased
        $messageRecorder
            ->expects($this->once())
            ->method('eraseMessages');

        /** @var stdClass[] $actuallyHandledMessages */
        $actuallyHandledMessages = [];
        $messageBus = $this->messageBusSpy($actuallyHandledMessages);
        $middleware = new HandlesRecordedMessagesMiddleware(
            $messageRecorder,
            $messageBus
        );

        $next = new CallableSpy();

        $middleware->handle($this->dummyMessage(), $next);
        $this->assertSame(1, $next->hasBeenCalled());
        $this->assertSame($messages, $actuallyHandledMessages);
    }

    /**
     * @test
     */
    public function itRethrowsACaughtExceptionButFirstClearsAnyRecordedMessages(): void
    {
        $messageRecorder = $this->mockMessageRecorder();

        $middleware = new HandlesRecordedMessagesMiddleware($messageRecorder, $this->dummyMessageBus());

        $exception = new Exception();
        $nextAlwaysFails = function () use ($exception) {
            throw $exception;
        };

        $messageRecorder
            ->expects($this->once())
            ->method('eraseMessages');

        try {
            $middleware->handle($this->dummyMessage(), $nextAlwaysFails);

            $this->fail('An exception should have been thrown');
        } catch (Exception $actualException) {
            $this->assertSame($exception, $actualException);
        }
    }

    /**
     * @param stdClass[] $actuallyHandledMessages
     *
     * @return MessageBus|MockObject
     */
    private function messageBusSpy(array &$actuallyHandledMessages)
    {
        $messageBus = $this->createMock(MessageBus::class);

        $messageBus
            ->expects($this->any())
            ->method('handle')
            ->will(
                $this->returnCallback(
                    function ($message) use (&$actuallyHandledMessages) {
                        $actuallyHandledMessages[] = $message;
                    }
                )
            );

        return $messageBus;
    }

    private function dummyMessage(): stdClass
    {
        return new stdClass();
    }

    /**
     * @return ContainsRecordedMessages|MockObject
     */
    private function mockMessageRecorder()
    {
        return $this->createMock(ContainsRecordedMessages::class);
    }

    /**
     * @return MessageBus|MockObject
     */
    private function dummyMessageBus()
    {
        return $this->createMock(MessageBus::class);
    }
}
