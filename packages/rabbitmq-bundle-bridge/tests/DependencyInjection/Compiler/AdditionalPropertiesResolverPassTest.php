<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use SimpleBus\RabbitMQBundleBridge\DependencyInjection\Compiler\AdditionalPropertiesResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @internal
 * @coversNothing
 */
class AdditionalPropertiesResolverPassTest extends TestCase
{
    private ContainerBuilder $container;

    private Definition $delegatingDefinition;

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
    public function itConfiguresAChainOfBusesAccordingToTheGivenPriorities(): void
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

    private function createResolver(string $class, int $priority): Definition
    {
        $definition = new Definition($class);
        $definition->addTag('simple_bus.additional_properties_resolver', ['priority' => $priority]);

        $this->container->setDefinition($class, $definition);

        return $definition;
    }

    /**
     * @param array<class-string, int> $expectedResolverClasses
     */
    private function resolverContainsResolvers(array $expectedResolverClasses): void
    {
        $actualResolverClasses = [];

        foreach ($this->delegatingDefinition->getArgument(0) as $argument) {
            $this->assertInstanceOf(
                'Symfony\Component\DependencyInjection\Definition',
                $argument
            );

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
