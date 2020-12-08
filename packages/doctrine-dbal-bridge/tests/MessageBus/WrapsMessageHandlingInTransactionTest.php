<?php

namespace SimpleBus\DoctrineDBALBridge\Tests\MessageBus;

use Error;
use Exception;
use PHPUnit\Framework\TestCase;
use SimpleBus\DoctrineDBALBridge\MessageBus\WrapsMessageHandlingInTransaction;
use Throwable;

class WrapsMessageHandlingInTransactionTest extends TestCase
{
    /**
     * @test
     */
    public function it_wraps_the_next_middleware_in_a_transaction()
    {
        $nextIsCalled = false;
        $message      = new \stdClass();

        $nextMiddlewareCallable = function (\stdClass $actualMessage) use ($message, &$nextIsCalled) {
            $this->assertSame($message, $actualMessage);
            $nextIsCalled = true;
        };

        $connection = $this->createMock('Doctrine\DBAL\Driver\Connection');
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

    public function errorProvider() : array
    {
        return [
            [new Exception()],
            [new Error()]
        ];
    }

    /**
     * @test
     * @dataProvider errorProvider
     */
    public function it_rolls_the_transaction_back_when_an_throwable_is_thrown(Throwable $error)
    {
        $message      = new \stdClass();

        $nextMiddlewareCallable = function () use ($error) {
            throw $error;
        };

        $connection = $this->createMock('Doctrine\DBAL\Driver\Connection');
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
