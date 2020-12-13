<?php

namespace SimpleBus\DoctrineDBALBridge\Tests\MessageBus;

use Doctrine\DBAL\Driver\Connection;
use Error;
use Exception;
use PHPUnit\Framework\TestCase;
use SimpleBus\DoctrineDBALBridge\MessageBus\WrapsMessageHandlingInTransaction;
use stdClass;
use Throwable;

/**
 * @internal
 * @coversNothing
 */
class WrapsMessageHandlingInTransactionTest extends TestCase
{
    /**
     * @test
     */
    public function itWrapsTheNextMiddlewareInATransaction(): void
    {
        $nextIsCalled = false;
        $message = new stdClass();

        $nextMiddlewareCallable = function (stdClass $actualMessage) use ($message, &$nextIsCalled) {
            $this->assertSame($message, $actualMessage);
            $nextIsCalled = true;
        };

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('beginTransaction');
        $connection
            ->expects($this->once())
            ->method('commit');

        $middleware = new WrapsMessageHandlingInTransaction($connection);

        $middleware->handle($message, $nextMiddlewareCallable);

        $this->assertTrue($nextIsCalled);
    }

    /**
     * @return array<Throwable[]>
     */
    public function errorProvider(): array
    {
        return [
            [
                new Exception(),
            ],
            [
                new Error(),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider errorProvider
     */
    public function itRollsTheTransactionBackWhenAnThrowableIsThrown(Throwable $error): void
    {
        $message = new stdClass();

        $nextMiddlewareCallable = function () use ($error) {
            throw $error;
        };

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('beginTransaction');
        $connection
            ->expects($this->once())
            ->method('rollback');

        $middleware = new WrapsMessageHandlingInTransaction($connection);

        try {
            $middleware->handle($message, $nextMiddlewareCallable);

            $this->fail('An exception should have been thrown');
        } catch (Throwable $actualError) {
            $this->assertSame($error, $actualError);
        }
    }
}
