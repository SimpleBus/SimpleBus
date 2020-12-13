<?php

namespace SimpleBus\AsynchronousBundle\Tests\Unit\DependencyInjection;

use LogicException;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use SimpleBus\AsynchronousBundle\DependencyInjection\SimpleBusAsynchronousExtension;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * @internal
 * @coversNothing
 */
class SimpleBusAsynchronousExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function itUsesStrategyAlwaysByDefault()
    {
        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle' => true, 'SimpleBusEventBusBundle' => true]);
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithTag('simple_bus.asynchronous.always_publishes_messages_middleware', 'event_bus_middleware', ['priority' => 0]);
    }

    /**
     * @test
     */
    public function itUsesStrategyPredefinedWhenConfigured()
    {
        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle' => true, 'SimpleBusEventBusBundle' => true]);
        $this->load(['events' => ['strategy' => 'predefined']]);

        $this->assertContainerBuilderHasServiceDefinitionWithTag('simple_bus.asynchronous.publishes_predefined_messages_middleware', 'event_bus_middleware', ['priority' => 0]);
    }

    /**
     * @test
     */
    public function itUsesCustomStrategyWhenConfigured()
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessageMatches('/.*custom_strategy.*/');

        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle' => true, 'SimpleBusEventBusBundle' => true]);
        $this->load(['events' => ['strategy' => ['strategy_service_id' => 'custom_strategy']]]);
    }

    /**
     * @test
     */
    public function itThrowsExceptionIfCommandBusBundleIsMissing()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/.*SimpleBusCommandBusBundle.*/');

        $this->container->setParameter('kernel.bundles', ['SimpleBusEventBusBundle' => true]);
        $this->load(['events' => ['strategy' => 'predefined']]);
    }

    /**
     * @test
     */
    public function itThrowsExceptionIfEventBusBundleIsMissing()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/.*SimpleBusEventBusBundle.*/');

        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle' => true]);
        $this->load(['events' => ['strategy' => 'predefined']]);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SimpleBusAsynchronousExtension('simple_bus_asynchronous'),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return ['object_serializer_service_id' => 'my_serializer', 'commands' => ['publisher_service_id' => 'pusher'], 'events' => ['publisher_service_id' => 'pusher']];
    }
}
