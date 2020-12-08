<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use Asynchronicity\PHPUnit\Eventually;
use SimpleBus\Asynchronous\Properties\DelegatingAdditionalPropertiesResolver;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Process\Process;

if (!class_exists(\PHPUnit_Framework_Constraint::class)) {
    class_alias(\PHPUnit\Framework\Constraint\Constraint::class, 'PHPUnit_Framework_Constraint');
}

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

    /**
     * Timeout for asynchronous tests
     *
     * @var int
     */
    private $timeoutMs = 10000;

    protected static function getKernelClass()
    {
        return 'SimpleBus\RabbitMQBundleBridge\Tests\Functional\TestKernel';
    }

    protected function setUp(): void
    {
        static::bootKernel();

        $this->logger = static::$kernel->getContainer()->get('logger');
        $this->logger->clearFile();

        $process = new Process(
            ['php', 'console.php', 'rabbitmq:setup-fabric'],
            __DIR__
        );
        $process->run();
    }

    /**
     * @test
     */
    public function it_is_able_to_load_the_bundle()
    {
        /*
         * There's no need to do anything here. This alone will prove that the bundle behaves well,
         * i.e. its services and configuration can be loaded.
         */

        $this->assertTrue(true);
    }

    /**
     * @test
     * @group functional
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
     * @group functional
     */
    public function it_handles_events_asynchronously()
    {
        $this->consumeMessagesFromQueue('asynchronous_events');

        $this->eventBus()->handle(new Event());

        $this->waitUntilLogFileContains('Notified of message');
    }

    /**
     * @test
     * @group functional
     */
    public function it_logs_errors()
    {
        $this->consumeMessagesFromQueue('asynchronous_commands');

        $this->commandBus()->handle(new AlwaysFailingCommand());

        $this->waitUntilLogFileContains('Failed to handle a message');
    }

    /**
     * @test
     * @group functional
     */
    public function it_resolve_properties()
    {
        $data = $this->additionalPropertiesResolver()->resolveAdditionalPropertiesFor($this->messageDummy());

        $this->assertSame(array('debug' => 'string'), $data);
    }

    /**
     * @test
     * @group functional
     */
    public function it_sends_properties_to_producer()
    {
        $container = static::$kernel->getContainer();
        $container->set('old_sound_rabbit_mq.asynchronous_commands_producer', $container->get('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver.producer_mock'));

        $this->commandBus()->handle(new AsynchronousCommand());

        $data = $container->get('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver.producer_mock')->getAdditionalProperties();
        $this->assertSame(array('debug' => 'string'), $data);
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
            new Eventually($this->timeoutMs, 100),
            sprintf('The log file does not contain "%s"', $message)
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
     * @return DelegatingAdditionalPropertiesResolver
     */
    private function additionalPropertiesResolver()
    {
        return static::$kernel->getContainer()->get('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver.public');
    }

    /**
     * @return \stdClass()
     */
    private function messageDummy()
    {
        return new \stdClass();
    }

    /**
     * @param $queue
     * @return Process
     */
    private function consumeMessagesFromQueue($queue)
    {
        $this->process = new Process(
            ['php', 'console.php', 'rabbitmq:consumer', $queue],
            __DIR__
        );

        $this->process->start();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
        static::$kernel = null;

        if ($this->process instanceof Process) {
            $this->process->stop(2, SIGKILL);
        }
    }
}
