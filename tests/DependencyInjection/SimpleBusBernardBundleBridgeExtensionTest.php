<?php

namespace SimpleBus\BernardBundleBridge\Tests\DependencyInjection;

use SimpleBus\BernardBundleBridge\DependencyInjection\SimpleBusBernardBundleBridgeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SimpleBusBernardBundleBridgeExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var SimpleBusBernardBundleBridgeExtension */
    private $extension;

    /** @var ContainerBuilder */
    private $container;

    public function setUp()
    {
        $this->extension = new SimpleBusBernardBundleBridgeExtension('prefix');
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.debug', false);
    }

    /**
     * @test
     */
    public function it_should_register_queue_name_resolvers_by_default()
    {
        $config = [];
        $this->extension->load([$config], $this->container);

        foreach (['class_based', 'fixed', 'mapped'] as $type) {
            $this->assertInstanceOf(
                'SimpleBus\Asynchronous\Routing\RoutingKeyResolver',
                $this->container->get(sprintf('simple_bus.bernard_bundle_bridge.routing.%s_queue_name_resolver', $type))
            );
        }

        // No other services are registered.
        $this->assertCount(3, $this->container->getDefinitions());
    }

    /**
     * @test
     */
    public function it_should_register_rot13_encrypter()
    {
        $config = [
            'encryption' => [
                'encrypter' => 'rot13',
            ],
        ];
        $this->extension->load([$config], $this->container);

        $this->assertTrue($this->container->hasAlias('simple_bus.bernard_bundle_bridge.encrypter'));
        $this->assertInstanceOf(
            'SimpleBus\BernardBundleBridge\Encrypter\Rot13Encrypter',
            $this->container->get('simple_bus.bernard_bundle_bridge.encrypter')
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

        $config = [
            'encryption' => [
                'encrypter' => 'nelmio',
                'secret' => '__secret__',
                'algorithm' => MCRYPT_DES,
            ],
        ];
        $this->extension->load([$config], $this->container);

        $this->assertInstanceOf(
            'SimpleBus\BernardBundleBridge\Encrypter\NelmioEncrypter',
            $this->container->get('simple_bus.bernard_bundle_bridge.encrypter')
        );

        $definition = $this->container->findDefinition('simple_bus.bernard_bundle_bridge.encrypter');

        $this->assertEquals('__secret__', $definition->getArgument(0));
        $this->assertEquals(MCRYPT_DES, $definition->getArgument(1));
    }

    /**
     * @test
     */
    public function it_should_register_logger_listener()
    {
        $config = ['logger' => 'custom_logger'];
        $this->extension->load([$config], $this->container);

        $this->assertTrue($this->container->hasDefinition('simple_bus.bernard_bundle_bridge.listener.logger'));
        $this->assertEquals(
            'custom_logger',
            $this->container->getDefinition('simple_bus.bernard_bundle_bridge.listener.logger')->getArgument(0)
        );
    }

    /**
     * @test
     */
//    public function it_should_register_disable_doctrine_logging_listener_in_debug_mode()
//    {
//        $config = [];
//        $this->container->setParameter('kernel.debug', true);
//        $this->extension->load([$config], $this->container);
//
//        $this->assertTrue($this->container->hasDefinition('simple_bus.bernard_bundle_bridge.listener.disable_doctrine_logger'));
//    }

    /**
     * @test
     */
    public function is_should_register_async_commands()
    {
        $config = ['commands' => true];
        $this->extension->load([$config], $this->container);

        $this->assertTrue($this->container->hasDefinition('simple_bus.bernard_bundle_bridge.command_publisher'));
        $this->assertFalse($this->container->hasDefinition('simple_bus.bernard_bundle_bridge.event_publisher'));
        $this->assertEquals('command', $this->container->getDefinition('simple_bus.bernard_bundle_bridge.command_publisher')->getArgument(3));
    }

    /**
     * @test
     */
    public function is_should_register_async_events()
    {
        $config = ['events' => true];
        $this->extension->load([$config], $this->container);

        $this->assertTrue($this->container->hasDefinition('simple_bus.bernard_bundle_bridge.event_publisher'));
        $this->assertFalse($this->container->hasDefinition('simple_bus.bernard_bundle_bridge.command_publisher'));
        $this->assertEquals('event', $this->container->getDefinition('simple_bus.bernard_bundle_bridge.event_publisher')->getArgument(3));
    }
}
