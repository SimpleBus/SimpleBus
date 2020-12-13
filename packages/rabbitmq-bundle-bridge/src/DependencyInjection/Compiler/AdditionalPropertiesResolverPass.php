<?php

namespace SimpleBus\RabbitMQBundleBridge\DependencyInjection\Compiler;

use SplPriorityQueue;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AdditionalPropertiesResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $delegatingResolverId = 'simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver';
        if (!($container->has($delegatingResolverId))) {
            return;
        }

        $resolverReferences = new SplPriorityQueue();

        foreach ($container->findTaggedServiceIds('simple_bus.additional_properties_resolver') as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {
                $priority = $tagAttributes['priority'] ?? 0;
                $resolverReferences->insert(new Reference($serviceId), $priority);
            }
        }

        $delegatingCleaner = $container->findDefinition($delegatingResolverId);
        $delegatingCleaner->replaceArgument(0, iterator_to_array($resolverReferences, false));
    }
}
