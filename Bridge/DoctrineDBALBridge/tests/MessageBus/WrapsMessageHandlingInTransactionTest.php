<?php

/**
 * @license https://github.com/SimpleBus/DoctrineDBALBridge/blob/master/LICENSE MIT
 */

namespace SimpleBus\DoctrineDBALBridge\Tests\MessageBus;

use SimpleBus\DoctrineDBALBridge\MessageBus\WrapsMessageHandlingInTransaction;
use SimpleBus\Message\Message;

/**
 * @author Jasper N. Brouwer <jasper@nerdsweide.nl>
 */
class WrapsMessageHandlingInTransactionTest extends \PHPUnit\Framework\TestCase
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

    /**
     * @test
     */
    public function it_rolls_the_transaction_back_when_an_exception_is_thrown()
    {
        $exception = new \Exception();
        $message      = new \stdClass();

        $nextMiddlewareCallable = function () use ($exception) {
            throw $exception;
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
        } catch (\Exception $actualException) {
            $this->assertSame($exception, $actualException);
        }
    }
}
