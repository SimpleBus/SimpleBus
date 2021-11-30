<?php

namespace SimpleBus\AsynchronousBundle\Tests\Unit\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use SimpleBus\AsynchronousBundle\DependencyInjection\Compiler\CollectAsynchronousEventNames;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class CollectAsynchronousEventNamesTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function ifCompilerPassCollectsEventNames(): void
    {
        $serviceId = 'simple_bus.asynchronous.publishes_predefined_messages_middleware';
        $middleware = new Definition();
        $middleware->addArgument('arg0');
        $middleware->addArgument('arg1');
        $middleware->addArgument([]);
        $this->setDefinition($serviceId, $middleware);

        $subscriber = new Definition();
        $subscriber->addTag('asynchronous_event_subscriber', ['subscribes_to' => 'foo']);
        $this->setDefinition('event_subscriber', $subscriber);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument($serviceId, 2, ['foo']);
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CollectAsynchronousEventNames());
    }
}
