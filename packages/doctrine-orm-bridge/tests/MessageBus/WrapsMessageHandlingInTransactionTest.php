<?php

namespace SimpleBus\DoctrineORMBridge\Tests\MessageBus;

use Error;
use Exception;
use PHPUnit\Framework\TestCase;
use SimpleBus\DoctrineORMBridge\MessageBus\WrapsMessageHandlingInTransaction;
use Throwable;

class WrapsMessageHandlingInTransactionTest extends TestCase
{
    /**
     * @test
     */
    public function it_wraps_the_next_middleware_in_a_transaction()
    {
        $nextIsCalled = false;
        $message = $this->dummyMessage();
        $nextMiddlewareCallable = function ($actualMessage) use ($message, &$nextIsCalled) {
            $this->assertSame($message, $actualMessage);
            $nextIsCalled = true;
        };
        $managerRegistry = $this->createMock('Doctrine\Persistence\ManagerRegistry');
        $entityManagerName = 'default';
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['transactional'])
            ->getMock();
        $entityManager
            ->expects($this->once())
            ->method('transactional')
            ->will(
                $this->returnCallback(
                    function (callable $transactionalCallback) {
                        $transactionalCallback();
                    }
                )
            );

        $managerRegistry
            ->expects($this->any())
            ->method('getManager')
            ->with($entityManagerName)
            ->will($this->returnValue($entityManager));

        $middleware = new WrapsMessageHandlingInTransaction($managerRegistry, $entityManagerName);

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
    public function it_resets_the_entity_manager_if_the_transaction_fails(Throwable $error)
    {
        $message = $this->dummyMessage();
        $managerRegistry = $this->createMock('Doctrine\Persistence\ManagerRegistry');
        $entityManagerName = 'default';
        $alwaysFailingEntityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['transactional'])
            ->getMock();
        $alwaysFailingEntityManager
            ->expects($this->once())
            ->method('transactional')
            ->will(
                $this->returnCallback(
                    function () use ($error) {
                        throw $error;
                    }
                )
            );

        $managerRegistry
            ->expects($this->any())
            ->method('getManager')
            ->with($entityManagerName)
            ->will($this->returnValue($alwaysFailingEntityManager));

        $managerRegistry
            ->expects($this->once())
            ->method('resetManager')
            ->with($entityManagerName);

        $middleware = new WrapsMessageHandlingInTransaction($managerRegistry, $entityManagerName);

        try {
            $middleware->handle(
                $message,
                function () {
                }
            );
            $this->fail('An exception should have been thrown');
        } catch (Throwable $actualError) {
            $this->assertSame($error, $actualError);
        }
    }

    private function dummyMessage()
    {
        return $this->createMock('SimpleBus\DoctrineORMBridge\Tests\MessageBus\DummyMessage');
    }
}

class DummyMessage
{
}
