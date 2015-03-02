<?php

namespace SimpleBus\RabbitMQBundle\Tests\Functional;

use Matthias\PhpUnitAsynchronicity\Eventually;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Process\Process;

class SimpleBusRabbitMQBundleTest extends KernelTestCase
{
    protected static function getKernelClass()
    {
        return 'SimpleBus\RabbitMQBundle\Tests\Functional\TestKernel';
    }

    /**
     * @test
     */
    public function it_handles_commands_asynchronously()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $logger = $kernel->getContainer()->get('logger');
        /** @var FileLogger $logger */
        $logger->clearFile();

        $consumeEventsProcess = new Process(
            'php console.php rabbitmq:consume asynchronous_events',
            __DIR__
        );
        $consumeEventsProcess->start();
        $consumeCommandsProcess = new Process(
            'php console.php rabbitmq:consume asynchronous_commands',
            __DIR__
        );
        $consumeCommandsProcess->start();
        $kernel->getContainer()->get('command_bus')->handle(new AsynchronousCommand());

        self::assertThat(
            function () use ($logger) {
                return $logger->fileContains(
                    'debug No message handler found, trying to handle it asynchronously'
                );
            },
            new Eventually(5000, 100)
        );

        self::assertThat(
            function () use ($logger) {
                return $logger->fileContains(
                    'Handling message'
                );
            },
            new Eventually(5000, 100)
        );

        $kernel->getContainer()->get('event_bus')->handle(new Event());

        self::assertThat(
            function () use ($logger) {
                return $logger->fileContains(
                    'Notified of message'
                );
            },
            new Eventually(5000, 100)
        );

        $consumeEventsProcess->stop(2, SIGKILL);
        $consumeCommandsProcess->stop(2, SIGKILL);
    }

    /**
     * @test
     */
//    public function it_handles_events_asynchronously()
//    {
//        $kernel = $this->createKernel();
//        $kernel->boot();
//    }
}
