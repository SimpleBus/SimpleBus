<?php

namespace SimpleBus\RabbitMQBundleBridge;

use SimpleBus\RabbitMQBundleBridge\DependencyInjection\SimpleBusRabbitMQBundleBridgeExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusRabbitMQBundleBridgeBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SimpleBusRabbitMQBundleBridgeExtension('simple_bus_rabbit_mq_bundle_bridge');
    }
}
