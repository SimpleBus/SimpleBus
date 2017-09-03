<?php

namespace SimpleBus\BernardBundleBridge\Tests\EventListener;

use Bernard\Command\ConsumeCommand;
use Doctrine\DBAL\Connection;
use SimpleBus\BernardBundleBridge\EventListener\DisableDoctrineLoggerListener;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DisableDoctrineLoggerListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $config;

    /** @var EventDispatcher */
    private $dispatcher;

    public function setUp()
    {
        $this->config = $this->getMockBuilder('Doctrine\DBAL\Configuration')
            ->disableOriginalConstructor()
            ->createMock()
        ;

        $registry = $this->createMock('Doctrine\Common\Persistence\ConnectionRegistry');
        $registry
            ->expects($this->any())
            ->method('getConnections')
            ->willReturn([
                new Connection([], $this->createMock('Doctrine\DBAL\Driver'), $this->config),
                new Connection([], $this->createMock('Doctrine\DBAL\Driver'), $this->config),
                new Connection([], $this->createMock('Doctrine\DBAL\Driver'), $this->config),
            ])
        ;

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(new DisableDoctrineLoggerListener($registry));
    }

    /**
     * @test
     */
    public function it_should_turn_off_sql_logging()
    {
        $this->config
            ->expects($this->exactly(3))
            ->method('setSQLLogger')
            ->with($this->equalTo(null))
        ;

        $event = new ConsoleEvent(
            $this->getConsumeCommand(),
            $this->createMock('Symfony\Component\Console\Input\InputInterface'),
            $this->createMock('Symfony\Component\Console\Output\OutputInterface')
        );

        $this->dispatcher->dispatch(ConsoleEvents::COMMAND, $event);
    }

    /**
     * @test
     */
    public function it_should_leave_sql_logging_intact()
    {
        $this->config->expects($this->never())->method('setSQLLogger');

        $command = $this->getMockBuilder('Symfony\Component\Console\Command\Command')
            ->disableOriginalConstructor()
            ->createMock()
        ;

        $event = new ConsoleEvent(
            $command,
            $this->createMock('Symfony\Component\Console\Input\InputInterface'),
            $this->createMock('Symfony\Component\Console\Output\OutputInterface')
        );

        $this->dispatcher->dispatch(ConsoleEvents::COMMAND, $event);
    }

    /**
     * @return ConsumeCommand
     */
    private function getConsumeCommand()
    {
        return new ConsumeCommand(
            $this->getMockBuilder('Bernard\Consumer')->disableOriginalConstructor()->createMock(),
            $this->createMock('Bernard\QueueFactory')
        );
    }
}
