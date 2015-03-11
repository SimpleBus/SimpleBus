<?php

namespace SimpleBus\RabbitMQBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterErrorHandlers implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $delegatingErrorHandlerId = 'simple_bus.rabbit_mq.delegating_error_handler';
        if (!$container->has($delegatingErrorHandlerId)) {
            return;
        }

        $errorHandlers = [];
        foreach ($container->findTaggedServiceIds('simple_bus.rabbit_mq.error_handler') as $serviceId => $tags) {
            $errorHandlers[] = new Reference($serviceId);
        }
        $delegatingErrorHandler = $container->findDefinition($delegatingErrorHandlerId);
        $delegatingErrorHandler->replaceArgument(0, $errorHandlers);
    }
}
