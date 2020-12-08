<?php

namespace SimpleBus\RabbitMQBundleBridge\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder($this->alias);

        if (method_exists($treeBuilder, 'getRootNode')) {
            // Symfony 4.2 +
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // Symfony 4.1 and below
            $rootNode = $treeBuilder->root($this->alias);
        }

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('commands')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('producer_service_id')
                            ->isRequired()
                        ->end()
                        ->scalarNode('routing_key_resolver')
                            ->info('Can be "empty", "class_based" or a service id')
                            ->defaultValue(null)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('events')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('producer_service_id')
                            ->isRequired()
                        ->end()
                        ->scalarNode('routing_key_resolver')
                            ->info('Can be "empty", "class_based" or a service id')
                            ->defaultValue(null)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('logging')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('channel')
                            ->defaultValue('error')
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('routing_key_resolver')
                    ->info('Can be "empty", "class_based" or a service id')
                    ->defaultValue('empty')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
