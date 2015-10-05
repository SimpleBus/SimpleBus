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
                ->scalarNode('queue_name_resolver')
                    ->info('Can be "default", "mapped" or a service id.')
                    ->defaultValue('default')
                ->end()
                ->arrayNode('queues_map')
                    ->useAttributeAsKey(true)
                    ->prototype('scalar')->isRequired()->cannotBeEmpty()->end()
                ->end()
                ->scalarNode('logger')->cannotBeEmpty()->end()
                ->arrayNode('encryption')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('encrypter')
                            ->info('Can be "nelmio" or a service id.')
                            ->defaultValue('nelmio')
                        ->end()
                        ->scalarNode('algorithm')->defaultValue('rijndael-128')->end()
                        ->scalarNode('secret')->defaultValue('%kernel.secret%')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
