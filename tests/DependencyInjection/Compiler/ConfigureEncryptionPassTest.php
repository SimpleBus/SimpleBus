<?php

namespace SimpleBus\BernardBundleBridge\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use SimpleBus\BernardBundleBridge\DependencyInjection\Compiler\ConfigureEncryptionPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ConfigureEncryptionPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigureEncryptionPass());
    }

    /**
     * @test
     */
    public function it_should_replace_original_object_serializer()
    {
        $loader = new XmlFileLoader($this->container, new FileLocator(__DIR__.'/../../../src/Resources/config'));
        $loader->load('encryption.xml');

        $serializer = $this->registerService('simple_bus.asynchronous.object_serializer', 'ObjectSerializer');

        $this->compile();

        $this->assertContainerBuilderHasAlias(
            'simple_bus.asynchronous.object_serializer',
            'simple_bus.bernard_bundle_bridge.encrypted_serializer'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'simple_bus.bernard_bundle_bridge.encrypted_serializer',
            0,
            $serializer
        );
    }
}
