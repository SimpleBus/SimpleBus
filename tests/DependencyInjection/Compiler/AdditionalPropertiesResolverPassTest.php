<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\DependencyInjection\Compiler;

use SimpleBus\RabbitMQBundleBridge\DependencyInjection\Compiler\AdditionalPropertiesResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AdditionalPropertiesResolverPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var Definition
     */
    private $delegatingDefinition;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->delegatingDefinition = new Definition('stdClass', array(array()));
        $this->container->setDefinition('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver', $this->delegatingDefinition);
        $this->container->addCompilerPass(new AdditionalPropertiesResolverPass());
    }

    /**
     * @test
     * @group test
     */
    public function it_configures_a_chain_of_buses_according_to_the_given_priorities()
    {
        $this->createResolver('resolver100', 100);
        $this->createResolver('resolver-100', -100);
        $this->createResolver('resolver200', 200);

        $this->container->compile();

        $this->resolverContainsResolvers(array('resolver200', 'resolver100', 'resolver-100'));
    }

    private function createResolver($id, $priority)
    {
        $definition = new Definition('stdClass');
        $definition->addTag('simple_bus.additional_properties_resolver', array('priority' => $priority));

        $this->container->setDefinition($id, $definition);

        return $definition;
    }

    private function resolverContainsResolvers($expectedResolverIds)
    {
        $actualResolverIds = [];

        foreach ($this->delegatingDefinition->getArgument(0) as $argument) {
            $this->assertInstanceOf(
                'Symfony\Component\DependencyInjection\Reference',
                $argument
            );
            $actualResolverIds[] = (string) $argument;
        }

        $this->assertEquals($expectedResolverIds, $actualResolverIds);
    }
}
