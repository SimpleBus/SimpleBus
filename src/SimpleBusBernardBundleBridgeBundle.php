<?php

namespace SimpleBus\BernardBundleBridge;

use SimpleBus\BernardBundleBridge\DependencyInjection\Compiler\ConfigureBernardPass;
use SimpleBus\BernardBundleBridge\DependencyInjection\Compiler\ConfigureEncryptionPass;
use SimpleBus\BernardBundleBridge\DependencyInjection\SimpleBusBernardBundleBridgeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusBernardBundleBridgeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new ConfigureBernardPass())
            ->addCompilerPass(new ConfigureEncryptionPass())
        ;
    }

    public function getContainerExtension()
    {
        return new SimpleBusBernardBundleBridgeExtension('simple_bus_bernard_bundle_bridge');
    }
}
