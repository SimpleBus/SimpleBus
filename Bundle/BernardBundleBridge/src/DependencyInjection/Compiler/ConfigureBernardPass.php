<?php

namespace SimpleBus\BernardBundleBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigureBernardPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('bernard.router')) {
            $container->getDefinition('bernard.router')->setClass('SimpleBus\BernardBundleBridge\BernardRouter');
        }
    }
}
