<?php

namespace SimpleBus\BernardBundleBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigureDisableDoctrineLoggerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
    }
}
