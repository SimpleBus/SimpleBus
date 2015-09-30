<?php

namespace SimpleBus\BernardBundleBridge;

use SimpleBus\BernardBundleBridge\DependencyInjection\SimpleBusBernardBundleBridgeExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusBernardBundleBridgeBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SimpleBusBernardBundleBridgeExtension('simple_bus_bernard_bundle_bridge');
    }
}
