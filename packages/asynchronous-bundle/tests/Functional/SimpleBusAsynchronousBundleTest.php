<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimpleBusAsynchronousBundleTest extends KernelTestCase
{
    protected static function getKernelClass()
    {
        return 'SimpleBus\AsynchronousBundle\Tests\Functional\TestKernel';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
        static::$kernel = null;
    }

    /**
     * @test
     */
    public function itNotifiesSynchronousEventSubscribersAndPublishesEvents()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $event = new DummyEvent();
        /** @var MessageBus $eventBus */
        $eventBus = $kernel->getContainer()->get('event_bus');
        $eventBus->handle($event);

        /** @var Spy $spy */
        $spy = $kernel->getContainer()->get('spy');
        $this->assertSame([$event], $spy->handled);

        /** @var PublisherSpy $eventPublisher */
        $eventPublisher = $kernel->getContainer()->get('event_publisher_spy');
        $this->assertSame([$event], $eventPublisher->publishedMessages());
    }

    /**
     * @test
     */
    public function itNotifiesAsynchronousEventSubscribers()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $event = new DummyEvent();
        /** @var MessageBus $asynchronousEventBus */
        $asynchronousEventBus = $kernel->getContainer()->get('asynchronous_event_bus');
        $asynchronousEventBus->handle($event);

        /** @var Spy $spy */
        $spy = $kernel->getContainer()->get('spy');
        $this->assertSame([$event], $spy->handled);

        /** @var PublisherSpy $eventPublisher */
        $eventPublisher = $kernel->getContainer()->get('event_publisher_spy');
        $this->assertSame([], $eventPublisher->publishedMessages());
    }

    /**
     * @test
     */
    public function itOnlyPublishesUnhandledCommands()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $command = new DummyCommand();
        /** @var MessageBus $commandBus */
        $commandBus = $kernel->getContainer()->get('command_bus');
        $commandBus->handle($command);

        /** @var PublisherSpy $commandPublisher */
        $commandPublisher = $kernel->getContainer()->get('command_publisher_spy');
        $this->assertSame([$command], $commandPublisher->publishedMessages());
    }

    /**
     * @test
     */
    public function itHandlesAsynchronousCommands()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $command = new DummyCommand();
        /** @var MessageBus $asynchronousCommandBus */
        $asynchronousCommandBus = $kernel->getContainer()->get('asynchronous_command_bus');
        $asynchronousCommandBus->handle($command);

        /** @var PublisherSpy $commandPublisher */
        $commandPublisher = $kernel->getContainer()->get('command_publisher_spy');
        $this->assertSame([], $commandPublisher->publishedMessages());

        /** @var Spy $spy */
        $spy = $kernel->getContainer()->get('spy');
        $this->assertSame([$command], $spy->handled);
    }

    /**
     * @test
     */
    public function itConsumesAsynchronousCommands()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $command = new DummyCommand();
        $envelope = DefaultEnvelope::forSerializedMessage(get_class($command), serialize($command));

        $commandConsumer = $kernel->getContainer()->get('asynchronous_command_consumer');
        $commandConsumer->consume(serialize($envelope));

        /** @var Spy $spy */
        $spy = $kernel->getContainer()->get('spy');
        $this->assertEquals([new DummyCommand()], $spy->handled);
    }

    /**
     * @test
     */
    public function itConsumesAsynchronousEvents()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $event = new DummyEvent();
        $envelope = DefaultEnvelope::forSerializedMessage(get_class($event), serialize($event));

        $commandConsumer = $kernel->getContainer()->get('asynchronous_event_consumer');
        $commandConsumer->consume(serialize($envelope));

        /** @var Spy $spy */
        $spy = $kernel->getContainer()->get('spy');
        $this->assertEquals([$event], $spy->handled);
    }
}
