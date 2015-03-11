<?php

namespace SimpleBus\RabbitMQBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root($this->alias);
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('commands')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('producer_service_id')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('events')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('producer_service_id')
                            ->isRequired()
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
                ->enumNode('routing_key')
                    ->values(['class_based', 'empty'])
                    ->defaultValue('empty')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
