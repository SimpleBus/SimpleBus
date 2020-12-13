<?php

namespace SimpleBus\RabbitMQBundleBridge\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private string $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->alias);
        $rootNode = $treeBuilder->getRootNode();

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
