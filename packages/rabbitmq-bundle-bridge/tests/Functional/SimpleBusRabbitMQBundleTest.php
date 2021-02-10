<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use Asynchronicity\PHPUnit\Eventually;
use SimpleBus\Asynchronous\Properties\DelegatingAdditionalPropertiesResolver;
use SimpleBus\Message\Bus\MessageBus;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Process\Process;

/**
 * @internal
 * @coversNothing
 */
class SimpleBusRabbitMQBundleTest extends KernelTestCase
{
    private FileLogger $logger;

    /**
     * @var null|Process<\Generator>
     */
    private ?Process $process = null;

    /**
     * Timeout for asynchronous tests.
     */
    private int $timeoutMs = 10000;

    protected function setUp(): void
    {
        static::bootKernel();

        $logger = static::$kernel->getContainer()->get('logger');

        $this->assertInstanceof(FileLogger::class, $logger);

        $this->logger = $logger;
        $this->logger->clearFile();

        $process = new Process(
            ['php', 'console.php', 'rabbitmq:setup-fabric'],
            __DIR__
        );
        $process->run();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;

        if ($this->process instanceof Process) {
            $this->process->stop(2, SIGKILL);
        }
    }

    /**
     * @test
     */
    public function itIsAbleToLoadTheBundle(): void
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
    public function itHandlesCommandsAsynchronously(): void
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
    public function itHandlesEventsAsynchronously(): void
    {
        $this->consumeMessagesFromQueue('asynchronous_events');

        $this->eventBus()->handle(new Event());

        $this->waitUntilLogFileContains('Notified of message');
    }

    /**
     * @test
     * @group functional
     */
    public function itLogsErrors(): void
    {
        $this->consumeMessagesFromQueue('asynchronous_commands');

        $this->commandBus()->handle(new AlwaysFailingCommand());

        $this->waitUntilLogFileContains('Failed to handle a message');
    }

    /**
     * @test
     * @group functional
     */
    public function itResolveProperties(): void
    {
        $data = $this->additionalPropertiesResolver()->resolveAdditionalPropertiesFor($this->messageDummy());

        $this->assertSame(['debug' => 'string'], $data);
    }

    /**
     * @test
     * @group functional
     */
    public function itSendsPropertiesToProducer(): void
    {
        $container = static::$kernel->getContainer();
        $container->set('old_sound_rabbit_mq.asynchronous_commands_producer', $container->get('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver.producer_mock'));

        $this->commandBus()->handle(new AsynchronousCommand());

        $producer = $container->get('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver.producer_mock');

        $this->assertInstanceOf(AdditionalPropertiesResolverProducerMock::class, $producer);

        $data = $producer->getAdditionalProperties();
        $this->assertSame(['debug' => 'string'], $data);
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    private function waitUntilLogFileContains(string $message): void
    {
        self::assertThat(
            function () use ($message) {
                $this->logger->fileContains($message);
            },
            new Eventually($this->timeoutMs, 100),
            sprintf('The log file does not contain "%s"', $message)
        );
    }

    private function commandBus(): MessageBus
    {
        $commandBus = static::$kernel->getContainer()->get('command_bus');

        $this->assertInstanceOf(MessageBus::class, $commandBus);

        return $commandBus;
    }

    private function eventBus(): MessageBus
    {
        $eventBus = static::$kernel->getContainer()->get('event_bus');

        $this->assertInstanceOf(MessageBus::class, $eventBus);

        return $eventBus;
    }

    private function additionalPropertiesResolver(): DelegatingAdditionalPropertiesResolver
    {
        $resolver = static::$kernel->getContainer()->get('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver.public');

        $this->assertInstanceOf(DelegatingAdditionalPropertiesResolver::class, $resolver);

        return $resolver;
    }

    private function messageDummy(): stdClass
    {
        return new stdClass();
    }

    private function consumeMessagesFromQueue(string $queue): void
    {
        $this->process = new Process(
            ['php', 'console.php', 'rabbitmq:consumer', $queue],
            __DIR__
        );

        $this->process->start();
    }
}
