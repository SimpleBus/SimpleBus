<?php

namespace SimpleBus\RabbitMQBundle\Tests\Functional;

use Matthias\PhpUnitAsynchronicity\Eventually;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Process\Process;

class SimpleBusRabbitMQBundleTest extends KernelTestCase
{
    /**
     * @var FileLogger
     */
    private $logger;

    /**
     * @var Process
     */
    private $process;

    protected static function getKernelClass()
    {
        return 'SimpleBus\RabbitMQBundle\Tests\Functional\TestKernel';
    }

    protected function setUp()
    {
        static::bootKernel();

        $this->logger = static::$kernel->getContainer()->get('logger');
        $this->logger->clearFile();
    }

    /**
     * @test
     */
    public function it_handles_commands_asynchronously()
    {
        $this->consumeMessagesFromQueue('asynchronous_commands');

        $this->commandBus()->handle(new AsynchronousCommand());

        $this->waitUntilLogFileContains('debug No message handler found, trying to handle it asynchronously');

        $this->waitUntilLogFileContains('Handling message');
    }

    /**
     * @test
     */
    public function it_handles_events_asynchronously()
    {
        $this->consumeMessagesFromQueue('asynchronous_events');

        $this->eventBus()->handle(new Event());

        $this->waitUntilLogFileContains('Notified of message');
    }

    /**
     * @param $message
     */
    private function waitUntilLogFileContains($message)
    {
        self::assertThat(
            function () use ($message) {

                return $this->logger->fileContains(
                    $message
                );
            },
            new Eventually(10000, 100)
        );
    }

    /**
     * @return MessageBus
     */
    private function commandBus()
    {
        return static::$kernel->getContainer()->get('command_bus');
    }

    /**
     * @return MessageBus
     */
    private function eventBus()
    {
        return static::$kernel->getContainer()->get('event_bus');
    }

    /**
     * @param $queue
     * @return Process
     */
    private function consumeMessagesFromQueue($queue)
    {
        $this->process = new Process(
            'php console.php rabbitmq:consume ' . $queue,
            __DIR__
        );
        $this->process->start();
    }

    protected function tearDown()
    {
        parent::tearDown();

        if ($this->process instanceof Process) {
            $this->process->stop(2, SIGKILL);
        }
    }
}
