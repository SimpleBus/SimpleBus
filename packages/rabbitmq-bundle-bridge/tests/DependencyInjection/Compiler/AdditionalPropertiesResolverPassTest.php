<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use SimpleBus\RabbitMQBundleBridge\DependencyInjection\Compiler\AdditionalPropertiesResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel;

class AdditionalPropertiesResolverPassTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var Definition
     */
    private $delegatingDefinition;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->delegatingDefinition = new Definition('stdClass', [[]]);
        $this->delegatingDefinition->setPublic(true);
        $this->container->setDefinition('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver', $this->delegatingDefinition);
        $this->container->addCompilerPass(new AdditionalPropertiesResolverPass());
    }

    /**
     * @test
     */
    public function itConfiguresAChainOfBusesAccordingToTheGivenPriorities()
    {
        $classes = [
            Resolver1::class => 100,
            Resolver2::class => -100,
            Resolver3::class => 200,
        ];

        foreach ($classes as $class => $priority) {
            $this->createResolver($class, $priority);
        }

        $this->container->compile();

        $this->resolverContainsResolvers($classes);
    }

    private function createResolver($class, $priority)
    {
        $definition = new Definition($class);
        $definition->addTag('simple_bus.additional_properties_resolver', ['priority' => $priority]);

        $this->container->setDefinition($class, $definition);

        return $definition;
    }

    private function resolverContainsResolvers($expectedResolverClasses)
    {
        $actualResolverClasses = [];

        foreach ($this->delegatingDefinition->getArgument(0) as $argument) {
            if (Kernel::VERSION_ID >= 40000) {
                $this->assertInstanceOf(
                    'Symfony\Component\DependencyInjection\Definition',
                    $argument
                );
            } else {
                $this->assertInstanceOf(
                    'Symfony\Component\DependencyInjection\Reference',
                    $argument
                );
                $argument = $this->container->getDefinition((string) $argument);
            }

            $actualResolverClasses[$argument->getClass()] = $argument->getTag('simple_bus.additional_properties_resolver')[0]['priority'];
        }

        $this->assertEquals($expectedResolverClasses, $actualResolverClasses);
    }
}

class Resolver1
{
}

class Resolver2
{
}

class Resolver3
{
}
