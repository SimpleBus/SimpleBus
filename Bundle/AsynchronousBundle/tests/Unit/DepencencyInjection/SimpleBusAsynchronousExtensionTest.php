<?php

namespace SimpleBus\AsynchronousBundle\Tests\Unit\DependencyInjection;


use LogicException;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use SimpleBus\AsynchronousBundle\DependencyInjection\SimpleBusAsynchronousExtension;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class SimpleBusAsynchronousExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions() : array
    {
        return array(
            new SimpleBusAsynchronousExtension('simple_bus_asynchronous')
        );
    }

    protected function getMinimalConfiguration() : array
    {
        return ['object_serializer_service_id'=>'my_serializer', 'commands'=>['publisher_service_id'=>'pusher'], 'events'=>['publisher_service_id'=>'pusher']];
    }


    /**
     * @test
     */
    public function it_uses_strategy_always_by_default()
    {
        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle'=>true, 'SimpleBusEventBusBundle'=>true]);
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithTag('simple_bus.asynchronous.always_publishes_messages_middleware', 'event_bus_middleware', ['priority'=>0]);
    }

    /**
     * @test
     */
    public function it_uses_strategy_predefined_when_configured()
    {
        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle'=>true, 'SimpleBusEventBusBundle'=>true]);
        $this->load(['events'=>['strategy'=>'predefined']]);

        $this->assertContainerBuilderHasServiceDefinitionWithTag('simple_bus.asynchronous.publishes_predefined_messages_middleware', 'event_bus_middleware', ['priority'=>0]);
    }

    /**
     * @test
     */
    public function it_uses_custom_strategy_when_configured()
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessageMatches('/.*custom_strategy.*/');

        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle'=>true, 'SimpleBusEventBusBundle'=>true]);
        $this->load(['events'=>['strategy'=>['strategy_service_id'=>'custom_strategy']]]);
    }

    /**
     * @test
     */
    public function it_throws_exception_if_command_bus_bundle_is_missing()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/.*SimpleBusCommandBusBundle.*/');

        $this->container->setParameter('kernel.bundles', ['SimpleBusEventBusBundle'=>true]);
        $this->load(['events'=>['strategy'=>'predefined']]);
    }

    /**
     * @test
     */
    public function it_throws_exception_if_event_bus_bundle_is_missing()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/.*SimpleBusEventBusBundle.*/');

        $this->container->setParameter('kernel.bundles', ['SimpleBusCommandBusBundle'=>true]);
        $this->load(['events'=>['strategy'=>'predefined']]);
    }
}
