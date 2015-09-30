<?php

namespace SimpleBus\BernardBundleBridge\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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

        $root = $treeBuilder->root($this->alias);
        $root
            ->children()
                ->arrayNode('commands')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('producer_service_id')->isRequired()->end()
                    ->end()
                ->end()

                ->arrayNode('events')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('producer_service_id')->isRequired()->end()
                    ->end()
                ->end()

                ->scalarNode('queue_name_resolver')
                    ->info('Can be "default" or a service id.')
                    ->isRequired()
                    ->defaultValue('default')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
