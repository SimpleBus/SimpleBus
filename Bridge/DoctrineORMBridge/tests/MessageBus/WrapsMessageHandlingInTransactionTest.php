<?php

namespace SimpleBus\DoctrineORMBridge\Tests\MessageBus;

use Exception;
use SimpleBus\DoctrineORMBridge\MessageBus\WrapsMessageHandlingInTransaction;

class WrapsMessageHandlingInTransactionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_wraps_the_next_middleware_in_a_transaction()
    {
        $nextIsCalled = false;
        $message = $this->dummyMessage();
        $nextMiddlewareCallable = function($actualMessage) use ($message, &$nextIsCalled) {
            $this->assertSame($message, $actualMessage);
            $nextIsCalled = true;
        };
        $managerRegistry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $entityManagerName = 'default';
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['transactional'])
            ->getMock();
        $entityManager
            ->expects($this->at(0))
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

    /**
     * @test
     */
    public function it_resets_the_entity_manager_if_the_transaction_fails()
    {
        $message = $this->dummyMessage();
        $throwException = new Exception();
        $managerRegistry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $entityManagerName = 'default';
        $alwaysFailingEntityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['transactional'])
            ->getMock();
        $alwaysFailingEntityManager
            ->expects($this->at(0))
            ->method('transactional')
            ->will(
                $this->returnCallback(
                    function () use ($throwException) {
                        throw $throwException;
                    }
                )
            );

        $managerRegistry
            ->expects($this->any())
            ->method('getManager')
            ->with($entityManagerName)
            ->will($this->returnValue($alwaysFailingEntityManager));

        $managerRegistry
            ->expects($this->at(1))
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
        } catch (Exception $actualException) {
            $this->assertSame($throwException, $actualException);
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
