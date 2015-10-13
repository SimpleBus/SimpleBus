<?php

namespace SimpleBus\BernardBundleBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigureEncryptionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('simple_bus.bernard_bundle_bridge.encrypted_serializer')) {
            // Retrieve original serializer.
            $serializer = $container->findDefinition('simple_bus.asynchronous.object_serializer');

            // Use within encryption.
            $container
                ->getDefinition('simple_bus.bernard_bundle_bridge.encrypted_serializer')
                ->replaceArgument(0, $serializer)
            ;

            // Replace original serializer with encrypted one.
            $container->setAlias('simple_bus.asynchronous.object_serializer', 'simple_bus.bernard_bundle_bridge.encrypted_serializer');
        }
    }
}
