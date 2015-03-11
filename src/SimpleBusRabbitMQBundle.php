<?php

namespace SimpleBus\RabbitMQBundle;

use SimpleBus\RabbitMQBundle\DependencyInjection\Compiler\RegisterErrorHandlers;
use SimpleBus\RabbitMQBundle\DependencyInjection\SimpleBusRabbitMQExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusRabbitMQBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SimpleBusRabbitMQExtension('simple_bus_rabbit_mq');
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterErrorHandlers());
    }
}
