<?php

namespace SimpleBus\BernardBundleBridge\Tests\Functional;

use Matthias\PhpUnitAsynchronicity\Eventually;
use SimpleBus\BernardBundleBridge\Tests\Functional\Demo\AlwaysFailingCommand;
use SimpleBus\BernardBundleBridge\Tests\Functional\Demo\AsynchronousCommand;
use SimpleBus\BernardBundleBridge\Tests\Functional\Demo\Event;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Process\Process;

/**
 * @group functional
 */
class SimpleBusBernardBundleBridgeBundleTest extends KernelTestCase
{
    /**
     * @var FileLogger
     */
    private $logger;

    /**
     * @var Process
     */
    private $process;

    private $timeoutMs = 10000;

    /**
     * @test
     */
    public function it_handles_commands_asynchronously()
    {
        $this->consumeMessagesFromQueue('asynchronous_commands');

        $this->commandBus()->handle(new AsynchronousCommand());

        $this->waitUntilLogFileContains('debug No message handler found, trying to handle it asynchronously');
        $this->waitUntilLogFileContains('Produced command into "asynchronous_commands" queue');
        $this->waitUntilLogFileContains('Invoking command from "asynchronous_commands" queue');
        $this->waitUntilLogFileContains('Handling message');
    }

    /**
     * @test
     */
    public function it_handles_events_asynchronously()
    {
        $this->consumeMessagesFromQueue('asynchronous_events');

        $this->eventBus()->handle(new Event());

        $this->waitUntilLogFileContains('Produced event into "asynchronous_events" queue');
        $this->waitUntilLogFileContains('Invoking event from "asynchronous_events" queue');
        $this->waitUntilLogFileContains('Notified of message');
    }

    /**
     * @test
     */
    public function it_logs_errors()
    {
        $this->consumeMessagesFromQueue('asynchronous_commands');

        $this->commandBus()->handle(new AlwaysFailingCommand());

        $this->waitUntilLogFileContains('Produced command into "asynchronous_commands" queue');
        $this->waitUntilLogFileContains('Invoking command from "asynchronous_commands" queue');
        $this->waitUntilLogFileContains('Error processing command from "asynchronous_commands" queue');

        // This errors in PHP7 and hhvm. Due the way exception is serialized?
        // $this->waitUntilLogFileContains('I always fail');
    }

    public function setUp()
    {
        static::bootKernel();

        $this->logger = static::$kernel->getContainer()->get('logger');
        $this->logger->clearFile();
    }

    protected function tearDown()
    {
        parent::tearDown();

        if ($this->process instanceof Process) {
            $this->process->stop(2, SIGKILL);
        }
    }

    protected static function getKernelClass()
    {
        return 'SimpleBus\BernardBundleBridge\Tests\Functional\TestKernel';
    }

    private function waitUntilLogFileContains($message)
    {
        self::assertThat(
            function () use ($message) {
                return $this->logger->fileContains($message);
            },
            new Eventually($this->timeoutMs, 100),
            sprintf('The log file does not contain "%s"', $message)
        );
    }

    private function consumeMessagesFromQueue($queue)
    {
        $this->process = new Process('php console.php bernard:consume '.$queue, __DIR__);
        $this->process->start();
    }

    /**
     * @return \SimpleBus\Message\Bus\MessageBus
     */
    private function commandBus()
    {
        return static::$kernel->getContainer()->get('command_bus');
    }

    /**
     * @return \SimpleBus\Message\Bus\MessageBus
     */
    private function eventBus()
    {
        return static::$kernel->getContainer()->get('event_bus');
    }
}
