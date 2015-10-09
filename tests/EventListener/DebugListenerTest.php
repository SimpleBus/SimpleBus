<?php

namespace SimpleBus\BernardBundleBridge\tests\EventListener;

use Bernard\Command\ConsumeCommand;
use Doctrine\DBAL\Connection;
use SimpleBus\BernardBundleBridge\EventListener\DebugListener;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\DependencyInjection\Container;

class DebugListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var DebugListener */
    private $listener;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $config;

    public function setUp()
    {
        $this->config = $this->getMockBuilder('Doctrine\DBAL\Configuration')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $container = new Container();
        $container->set('doctrine.dbal.default_connection', new Connection([], $this->getMock('Doctrine\DBAL\Driver'), $this->config));

        $this->listener = new DebugListener($container);
    }

    public function testSQLLoggingIsTurnedOff()
    {
        $this->config
            ->expects($this->once())
            ->method('setSQLLogger')
            ->with($this->equalTo(null))
        ;

        $event = new ConsoleEvent(
            $this->getConsumeCommand(),
            $this->getMock('Symfony\Component\Console\Input\InputInterface'),
            $this->getMock('Symfony\Component\Console\Output\OutputInterface')
        );

        $this->listener->onCommand($event);
    }

    public function testSQLLoggingIsIntact()
    {
        $this->config->expects($this->never())->method('setSQLLogger');

        $command = $this->getMockBuilder('Symfony\Component\Console\Command\Command')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $event = new ConsoleEvent(
            $command,
            $this->getMock('Symfony\Component\Console\Input\InputInterface'),
            $this->getMock('Symfony\Component\Console\Output\OutputInterface')
        );

        $this->listener->onCommand($event);
    }

    /**
     * @return ConsumeCommand
     */
    private function getConsumeCommand()
    {
        return new ConsumeCommand(
            $this->getMockBuilder('Bernard\Consumer')->disableOriginalConstructor()->getMock(),
            $this->getMock('Bernard\QueueFactory')
        );
    }
}
