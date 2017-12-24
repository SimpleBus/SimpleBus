<?php

namespace SimpleBus\BernardBundleBridge\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use SimpleBus\BernardBundleBridge\DependencyInjection\Compiler\ConfigureBernardPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @group BernardBundleBridge
 */
class ConfigureBernardPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_should_overwrite_bernard_router_class()
    {
        $this->registerService('bernard.router', 'FooRouter');

        $this->compile();

        $this->assertContainerBuilderHasService('bernard.router', 'SimpleBus\BernardBundleBridge\BernardRouter');
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigureBernardPass());
    }
}
