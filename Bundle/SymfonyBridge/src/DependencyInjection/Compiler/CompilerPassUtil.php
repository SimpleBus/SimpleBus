<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

class CompilerPassUtil
{
    public static function prependBeforeOptimizationPass(
        ContainerBuilder $container,
        CompilerPassInterface $compilerPass
    ) {
        $rc = new \ReflectionMethod(PassConfig::class, 'addPass');
        if ($rc->getNumberOfParameters() >= 3 || method_exists(PassConfig::class, 'sortPasses')) {
            $container->addCompilerPass($compilerPass, PassConfig::TYPE_BEFORE_OPTIMIZATION, 50);
        } else {
            $compilerPassConfig = $container->getCompilerPassConfig();
            $beforeOptimizationPasses = $compilerPassConfig->getBeforeOptimizationPasses();
            array_unshift($beforeOptimizationPasses, $compilerPass);
            $compilerPassConfig->setBeforeOptimizationPasses($beforeOptimizationPasses);
        }
    }
}
