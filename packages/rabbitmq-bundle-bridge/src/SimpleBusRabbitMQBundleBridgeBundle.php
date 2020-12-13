<?php

namespace SimpleBus\RabbitMQBundleBridge;

use SimpleBus\RabbitMQBundleBridge\DependencyInjection\Compiler\AdditionalPropertiesResolverPass;
use SimpleBus\RabbitMQBundleBridge\DependencyInjection\SimpleBusRabbitMQBundleBridgeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusRabbitMQBundleBridgeBundle extends Bundle
{
    public function getContainerExtension(): SimpleBusRabbitMQBundleBridgeExtension
    {
        return new SimpleBusRabbitMQBundleBridgeExtension('simple_bus_rabbit_mq_bundle_bridge');
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AdditionalPropertiesResolverPass());
    }
}
