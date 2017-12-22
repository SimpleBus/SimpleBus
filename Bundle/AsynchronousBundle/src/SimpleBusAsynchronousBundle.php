<?php

namespace SimpleBus\AsynchronousBundle;

use SimpleBus\AsynchronousBundle\DependencyInjection\Compiler\CollectAsynchronousEventNames;
use SimpleBus\AsynchronousBundle\DependencyInjection\SimpleBusAsynchronousExtension;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\AutoRegister;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterHandlers;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterSubscribers;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusAsynchronousBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SimpleBusAsynchronousExtension('simple_bus_asynchronous');
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new ConfigureMiddlewares('simple_bus.asynchronous.command_bus', 'asynchronous_command_bus_middleware')
        );
        $container->addCompilerPass(
            new AutoRegister('asynchronous_command_handler', 'handles'),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            10
        );
        $container->addCompilerPass(
            new RegisterHandlers(
                'simple_bus.asynchronous.command_bus.command_handler_map',
                'asynchronous_command_handler',
                'handles'
            )
        );

        $container->addCompilerPass(
            new ConfigureMiddlewares('simple_bus.asynchronous.event_bus', 'asynchronous_event_bus_middleware')
        );
        $container->addCompilerPass(
            new AutoRegister('asynchronous_event_subscriber', 'subscribes_to'),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            10
        );
        $container->addCompilerPass(
            new RegisterSubscribers(
                'simple_bus.asynchronous.event_bus.event_subscribers_collection',
                'asynchronous_event_subscriber',
                'subscribes_to'
            )
        );

        $container->addCompilerPass(new CollectAsynchronousEventNames());
    }
}
