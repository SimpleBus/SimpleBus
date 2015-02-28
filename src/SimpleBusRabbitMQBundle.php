<?php

namespace SimpleBus\RabbitMQBundle;

use SimpleBus\RabbitMQBundle\DependencyExtension\SimpleBusRabbitMQExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusRabbitMQBundle extends Bundle
{
    private $configurationAlias;

    public function __construct($configurationAlias = 'simple_bus_rabbit_mq')
    {
        $this->configurationAlias = $configurationAlias;
    }

    public function getContainerExtension()
    {
        return new SimpleBusRabbitMQExtension($this->configurationAlias);
    }
}
