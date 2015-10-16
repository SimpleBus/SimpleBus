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
    public function it_should_register_queue_name_resolvers_by_default()
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

    protected function getContainerExtensions()
    {
        return [
            new SimpleBusBernardBundleBridgeExtension('simple_bus_bernard_bundle_bridge'),
        ];
    }
}
