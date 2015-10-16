<?php

namespace SimpleBus\BernardBundleBridge\tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use SimpleBus\BernardBundleBridge\DependencyInjection\SimpleBusBernardBundleBridgeExtension;

class SimpleBusBernardBundleBridgeExtensionTest extends AbstractExtensionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setParameter('kernel.debug', false);
    }

    /**
     * @test
     */
    public function it_should_register_queue_name_resolver_templates_by_default()
    {
        $this->load();

        foreach (['class_based', 'fixed', 'mapped'] as $type) {
            $serviceId = sprintf('simple_bus.bernard_bundle_bridge.routing.%s_queue_name_resolver', $type);
            $this->assertContainerBuilderHasService($serviceId);
        }

        // No other services are registered.
        $this->assertCount(3, $this->container->getDefinitions());
    }

    /**
     * @test
     */
    public function it_should_register_rot13_encrypter()
    {
        $this->load([
            'encryption' => [
                'encrypter' => 'rot13',
            ],
        ]);

        $this->assertContainerBuilderHasAlias(
            'simple_bus.bernard_bundle_bridge.encrypter',
            'simple_bus.bernard_bundle_bridge.encrypter.rot13'
        );
        $this->assertContainerBuilderHasService(
            'simple_bus.bernard_bundle_bridge.encrypter.rot13',
            'SimpleBus\BernardBundleBridge\Encrypter\Rot13Encrypter'
        );
    }

    /**
     * @test
     */
    public function it_should_register_nelmio_encrypter()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped();
        }

        $this->load([
            'encryption' => [
                'encrypter' => 'nelmio',
                'secret' => '__secret__',
                'algorithm' => MCRYPT_DES,
            ],
        ]);

        $this->assertContainerBuilderHasAlias(
            'simple_bus.bernard_bundle_bridge.encrypter',
            'simple_bus.bernard_bundle_bridge.encrypter.nelmio'
        );
        $this->assertContainerBuilderHasService(
            'simple_bus.bernard_bundle_bridge.encrypter.nelmio',
            'SimpleBus\BernardBundleBridge\Encrypter\NelmioEncrypter'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'simple_bus.bernard_bundle_bridge.encrypter.nelmio',
            0,
            '__secret__'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'simple_bus.bernard_bundle_bridge.encrypter.nelmio',
            1,
            MCRYPT_DES
        );
    }

    /**
     * @test
     */
    public function it_should_register_custom_encrypter()
    {
        $this->registerService('my_encrypter', 'FooEncrypter');

        $this->load([
            'encryption' => [
                'encrypter' => 'my_encrypter',
            ],
        ]);

        $this->assertContainerBuilderHasAlias('simple_bus.bernard_bundle_bridge.encrypter', 'my_encrypter');
    }

    /**
     * @test
     */
    public function it_should_register_logger_listener()
    {
        $this->load(['logger' => 'custom_logger']);

        $this->assertContainerBuilderHasService('simple_bus.bernard_bundle_bridge.listener.logger');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'simple_bus.bernard_bundle_bridge.listener.logger',
            0,
            'custom_logger'
        );
    }

    /**
     * @test
     */
    public function is_should_register_async_commands()
    {
        $this->load(['commands' => true]);

        $this->assertContainerBuilderHasService('simple_bus.bernard_bundle_bridge.command_publisher');
        $this->assertContainerBuilderNotHasService('simple_bus.bernard_bundle_bridge.event_publisher');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'simple_bus.bernard_bundle_bridge.command_publisher',
            3,
            'command'
        );
    }

    /**
     * @test
     */
    public function is_should_register_async_events()
    {
        $this->load(['events' => true]);

        $this->assertContainerBuilderHasService('simple_bus.bernard_bundle_bridge.event_publisher');
        $this->assertContainerBuilderNotHasService('simple_bus.bernard_bundle_bridge.command_publisher');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'simple_bus.bernard_bundle_bridge.event_publisher',
            3,
            'event'
        );
    }

    /**
     * @test
     */
    public function it_should_register_fixed_queue_name_resolver_by_default()
    {
        $this->load(['commands' => true]);

        $this->assertContainerBuilderHasServiceDefinitionWithParent(
            'simple_bus.bernard_bundle_bridge.routing.commands_queue_name_resolver',
            'simple_bus.bernard_bundle_bridge.routing.fixed_queue_name_resolver'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'simple_bus.bernard_bundle_bridge.routing.commands_queue_name_resolver',
            0,
            'asynchronous_commands'
        );
    }

    /**
     * @test
     */
    public function it_should_register_class_based_queue_name_resolver()
    {
        $this->load([
            'commands' => [
                'queue_name_resolver' => 'class_based',
            ],
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithParent(
            'simple_bus.bernard_bundle_bridge.routing.commands_queue_name_resolver',
            'simple_bus.bernard_bundle_bridge.routing.class_based_queue_name_resolver'
        );
    }

    /**
     * @test
     */
    public function it_should_register_mapped_queue_name_resolver()
    {
        $this->load([
            'commands' => [
                'queue_name_resolver' => 'mapped',
                'queues_map' => [
                    'Foo' => 'foo',
                    'Bar' => 'bar',
                ],
            ],
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithParent(
            'simple_bus.bernard_bundle_bridge.routing.commands_queue_name_resolver',
            'simple_bus.bernard_bundle_bridge.routing.mapped_queue_name_resolver'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'simple_bus.bernard_bundle_bridge.routing.commands_queue_name_resolver',
            0,
            ['Foo' => 'foo', 'Bar' => 'bar']
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'simple_bus.bernard_bundle_bridge.routing.commands_queue_name_resolver',
            1,
            'asynchronous_commands'
        );
    }

    /**
     * @test
     */
    public function is_should_register_custom_queue_name_resolver()
    {
        $this->registerService('my_queue_name_resolver', 'FooResolver');

        $this->load([
            'commands' => [
                'queue_name_resolver' => 'my_queue_name_resolver',
            ],
        ]);

        $this->assertContainerBuilderHasAlias(
            'simple_bus.bernard_bundle_bridge.routing.commands_queue_name_resolver',
            'my_queue_name_resolver'
        );
    }

    protected function getContainerExtensions()
    {
        return [
            new SimpleBusBernardBundleBridgeExtension('simple_bus_bernard_bundle_bridge'),
        ];
    }
}
