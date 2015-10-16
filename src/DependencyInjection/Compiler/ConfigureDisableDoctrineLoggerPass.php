<?php

namespace SimpleBus\BernardBundleBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Disable Doctrine logger when relevant Bernard driver is enabled.
 */
class ConfigureDisableDoctrineLoggerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('bernard.driver')) {
            return;
        }

        if ($container->findDefinition('bernard.driver')->getClass() === 'Bernard\Driver\DoctrineDriver') {
            $listenerClass = 'SimpleBus\BernardBundleBridge\EventListener\DisableDoctrineLoggerListener';

            $definition = new Definition($listenerClass, [new Reference('doctrine')]);

            $container->setDefinition(
                'simple_bus.bernard_bundle_bridge.listener.disable_doctrine_logger',
                $definition->addTag('kernel.event_subscriber')
            );
        }
    }
}
