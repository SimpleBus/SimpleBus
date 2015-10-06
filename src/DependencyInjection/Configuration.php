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
                    ->info('Class name to queue name hash.')
                    ->useAttributeAsKey(true)
                    ->prototype('scalar')->isRequired()->cannotBeEmpty()->end()
                ->end()

                ->scalarNode('logger')
                    ->info('Logger service id.')
                    ->cannotBeEmpty()
                ->end()

                ->arrayNode('encryption')
                    ->info('Encrypt messages on the wire.')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('encrypter')
                            ->info('Can be "nelmio", "rot13" or a service id.')
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
