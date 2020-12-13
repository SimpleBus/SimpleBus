<?php

namespace SimpleBus\AsynchronousBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder($this->alias);
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('object_serializer_service_id')
                    ->info('Service id of an instance of ObjectSerializer')
                    ->isRequired()
                ->end()
                ->arrayNode('commands')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('publisher_service_id')
                            ->info('Service id of an instance of Publisher')
                            ->isRequired()
                        ->end()
                        ->arrayNode('logging')
                            ->canBeEnabled()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('events')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('strategy')
                            ->addDefaultsIfNotSet()
                            ->beforeNormalization()
                                ->ifInArray(['always', 'predefined'])
                                ->then(function ($v) {
                                    $map = [
                                        'always' => 'simple_bus.asynchronous.always_publishes_messages_middleware',
                                        'predefined' => 'simple_bus.asynchronous.publishes_predefined_messages_middleware',
                                    ];

                                    return [
                                        'strategy_service_id' => $map[$v],
                                    ];
                                })
                            ->end()
                            ->info('What strategy to use to publish messages')
                            ->children()
                                ->scalarNode('strategy_service_id')
                                ->defaultValue('simple_bus.asynchronous.always_publishes_messages_middleware')
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('publisher_service_id')
                            ->info('Service id of an instance of Publisher')
                            ->isRequired()
                        ->end()
                        ->arrayNode('logging')
                            ->canBeEnabled()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
