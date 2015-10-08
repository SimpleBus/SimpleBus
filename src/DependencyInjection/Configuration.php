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
            ->addDefaultsIfNotSet()
            ->children()
                ->append($this->addConfigurationNode('commands'))
                ->append($this->addConfigurationNode('events'))

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


    private function addConfigurationNode($type)
    {
        $treeBuilder = new TreeBuilder();

        $node = $treeBuilder->root($type);
        $node
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifString()
                    ->then(function ($name) {
                        return array(
                            'queue_name_resolver' => 'fixed',
                            'queue_name' => $name,
                        );
                    })
            ->end()
            ->children()
                ->scalarNode('queue_name_resolver')
                    ->info('Can be "class_based", "mapped", "fixed" or a service id.')
                    ->defaultValue('fixed')
                    ->isRequired()
                ->end()
                ->scalarNode('queue_name')
                    ->info('This default name of the queue')
                    ->defaultValue(sprintf('asynchronous_%s', $type))
                ->end()
                ->arrayNode('queues_map')
                    ->info('Class name to queue name hash. If class is not found, "default" key is used.')
                    ->useAttributeAsKey(true)
                    ->prototype('scalar')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
