<?php

namespace SimpleBus\RabbitMQBundleBridge\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class SimpleBusRabbitMQBundleBridgeExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->requireBundle('SimpleBusAsynchronousBundle', $container);
        $this->requireBundle('OldSoundRabbitMqBundle', $container);

        // it's a shame we have to do this twice :)
        $configs = $container->getExtensionConfig($this->getAlias());
        $mergedConfig = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        if ($mergedConfig['commands']['enabled']) {
            $container->prependExtensionConfig(
                'simple_bus_asynchronous',
                [
                    'commands' => [
                        'publisher_service_id' => 'simple_bus.rabbit_mq_bundle_bridge.command_publisher'
                    ]
                ]
            );
        }

        if ($mergedConfig['events']['enabled']) {
            $container->prependExtensionConfig(
                'simple_bus_asynchronous',
                [
                    'events' => [
                        'publisher_service_id' => 'simple_bus.rabbit_mq_bundle_bridge.event_publisher'
                    ]
                ]
            );
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->alias);
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        foreach (['command' => 'commands', 'event' => 'events'] as $messageType => $configurationKey) {
            if (!$mergedConfig[$configurationKey]['enabled']) {
                continue;
            }

            $loader->load($configurationKey . '.yml');
            $container->setAlias(
                'simple_bus.rabbit_mq_bundle_bridge.' . $messageType . '_producer',
                $mergedConfig[$configurationKey]['producer_service_id']
            );
        }

        $loader->load('error_handling.yml');
        $loggerChannel = $mergedConfig['logging']['channel'];
        $container
            ->findDefinition('simple_bus.rabbit_mq_bundle_bridge.error_logging_event_subscriber')
            ->addTag(
                'monolog.logger',
                ['channel' => $loggerChannel]
            );

        $loader->load('routing.yml');
        if (in_array($mergedConfig['routing_key_resolver'], ['empty', 'class_based'])) {
            $routingKeyResolverId = sprintf(
                'simple_bus.rabbit_mq_bundle_bridge.routing.%s_routing_key_resolver',
                $mergedConfig['routing_key_resolver']
            );
        } else {
            $routingKeyResolverId = $mergedConfig['routing_key_resolver'];
        }
        $container->setAlias(
            'simple_bus.rabbit_mq_bundle_bridge.routing.routing_key_resolver',
             $routingKeyResolverId
        );

        $loader->load('properties.yml');
    }

    private function requireBundle($bundleName, ContainerBuilder $container)
    {
        $enabledBundles = $container->getParameter('kernel.bundles');
        if (!isset($enabledBundles[$bundleName])) {
            throw new \LogicException(
                sprintf(
                    'You need to enable "%s" as well',
                    $bundleName
                )
            );
        }
    }
}
