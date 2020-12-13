<?php

namespace SimpleBus\AsynchronousBundle\DependencyInjection\Compiler;

use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\CollectServices;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CollectAsynchronousEventNames implements CompilerPassInterface
{
    use CollectServices;

    /**
     * Get all asynchronous event subscribers and save the name of the event they are listening to.
     */
    public function process(ContainerBuilder $container): void
    {
        $serviceId = 'simple_bus.asynchronous.publishes_predefined_messages_middleware';
        if (!$container->hasDefinition($serviceId)) {
            return;
        }

        $names = [];
        $this->collectServiceIds(
            $container,
            'asynchronous_event_subscriber',
            'subscribes_to',
            function ($key) use (&$names) {
                $names[] = $key;
            }
        );

        $container->getDefinition($serviceId)->replaceArgument(2, array_unique($names));
    }
}
