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

        $synchronousEventSubscriber = $kernel->getContainer()->get('synchronous_event_subscriber_spy');
        /** @var EventSubscriberSpy $synchronousEventSubscriber */
        $this->assertSame([$event], $synchronousEventSubscriber->notifiedEvents());

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

        $asynchronousEventSubscriber = $kernel->getContainer()->get('asynchronous_event_subscriber_spy');
        /** @var EventSubscriberSpy $asynchronousEventSubscriber */
        $this->assertSame([$event], $asynchronousEventSubscriber->notifiedEvents());

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

        $asynchronousCommandHandlerSpy = $kernel->getContainer()->get('asynchronous_command_handler_spy');
        /** @var CommandHandlerSpy $asynchronousCommandHandlerSpy->handledCommands */
        $this->assertSame([$command], $asynchronousCommandHandlerSpy->handledCommands());
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

        $asynchronousCommandHandlerSpy = $kernel->getContainer()->get('asynchronous_command_handler_spy');
        /** @var CommandHandlerSpy $asynchronousCommandHandlerSpy->handledCommands */
        $this->assertEquals([new DummyCommand()], $asynchronousCommandHandlerSpy->handledCommands());
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

        $asynchronousEventSubscriber = $kernel->getContainer()->get('asynchronous_event_subscriber_spy');
        /** @var EventSubscriberSpy $asynchronousEventSubscriber */
        $this->assertEquals([$event], $asynchronousEventSubscriber->notifiedEvents());
    }
}
