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
    public function it_notifies_synchronous_event_subscribers_and_publishes_events()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $event = new DummyEvent();
        $eventBus = $kernel->getContainer()->get('event_bus');
        /** @var MessageBus $eventBus */
        $eventBus->handle($event);

        $spy = $kernel->getContainer()->get('spy');
        /** @var Spy $spy */
        $this->assertSame([$event], $spy->handled);

        $eventPublisher = $kernel->getContainer()->get('event_publisher_spy');
        /** @var PublisherSpy $eventPublisher */
        $this->assertSame([$event], $eventPublisher->publishedMessages());
    }

    /**
     * @test
     */
    public function it_notifies_asynchronous_event_subscribers()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $event = new DummyEvent();
        $asynchronousEventBus = $kernel->getContainer()->get('asynchronous_event_bus');
        /** @var MessageBus $asynchronousEventBus */
        $asynchronousEventBus->handle($event);

        $spy = $kernel->getContainer()->get('spy');
        /** @var Spy $spy */
        $this->assertSame([$event], $spy->handled);

        $eventPublisher = $kernel->getContainer()->get('event_publisher_spy');
        /** @var PublisherSpy $eventPublisher */
        $this->assertSame([], $eventPublisher->publishedMessages());
    }

    /**
     * @test
     */
    public function it_only_publishes_unhandled_commands()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $command = new DummyCommand();
        $commandBus = $kernel->getContainer()->get('command_bus');
        /** @var MessageBus $commandBus */
        $commandBus->handle($command);

        $commandPublisher = $kernel->getContainer()->get('command_publisher_spy');
        /** @var PublisherSpy $commandPublisher */
        $this->assertSame([$command], $commandPublisher->publishedMessages());
    }

    /**
     * @test
     */
    public function it_handles_asynchronous_commands()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $command = new DummyCommand();
        $asynchronousCommandBus = $kernel->getContainer()->get('asynchronous_command_bus');
        /** @var MessageBus $asynchronousCommandBus */
        $asynchronousCommandBus->handle($command);

        $commandPublisher = $kernel->getContainer()->get('command_publisher_spy');
        /** @var PublisherSpy $commandPublisher */
        $this->assertSame([], $commandPublisher->publishedMessages());

        $spy = $kernel->getContainer()->get('spy');
        /** @var Spy $spy */
        $this->assertSame([$command], $spy->handled);
    }

    /**
     * @test
     */
    public function it_consumes_asynchronous_commands()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $command = new DummyCommand();
        $envelope = DefaultEnvelope::forSerializedMessage(get_class($command), serialize($command));

        $commandConsumer = $kernel->getContainer()->get('asynchronous_command_consumer');
        $commandConsumer->consume(serialize($envelope));

        $spy = $kernel->getContainer()->get('spy');
        /** @var Spy $spy */
        $this->assertEquals([new DummyCommand()], $spy->handled);
    }

    /**
     * @test
     */
    public function it_consumes_asynchronous_events()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $event = new DummyEvent();
        $envelope = DefaultEnvelope::forSerializedMessage(get_class($event), serialize($event));

        $commandConsumer = $kernel->getContainer()->get('asynchronous_event_consumer');
        $commandConsumer->consume(serialize($envelope));

        $spy = $kernel->getContainer()->get('spy');
        /** @var Spy $spy */
        $this->assertEquals([$event], $spy->handled);
    }
}
