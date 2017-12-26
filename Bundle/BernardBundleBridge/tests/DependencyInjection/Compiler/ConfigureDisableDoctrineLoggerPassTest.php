<?php

namespace SimpleBus\BernardBundleBridge\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use SimpleBus\BernardBundleBridge\DependencyInjection\Compiler\ConfigureDisableDoctrineLoggerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigureDisableDoctrineLoggerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigureDisableDoctrineLoggerPass());
    }

    /**
     * @test
     */
    public function it_should_disable_doctrine_logger()
    {
        $this->registerService('bernard.driver', 'Bernard\Driver\DoctrineDriver');

        $this->compile();

        $this->assertContainerBuilderHasService(
            'simple_bus.bernard_bundle_bridge.listener.disable_doctrine_logger',
            'SimpleBus\BernardBundleBridge\EventListener\DisableDoctrineLoggerListener'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'simple_bus.bernard_bundle_bridge.listener.disable_doctrine_logger',
            'kernel.event_subscriber'
        );
    }
}
