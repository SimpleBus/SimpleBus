<?php

namespace SimpleBus\RabbitMQBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class SimpleBusRabbitMQExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function prepend(ContainerBuilder $container)
    {
        // TODO allow enabling either commands or events
        $container
            ->prependExtensionConfig(
                'simple_bus_asynchronous',
                [
                    'commands' => [
                        'publisher_service_id' => 'simple_bus.rabbit_mq.command_publisher'
                    ],
                    'events' => [
                        'publisher_service_id' => 'simple_bus.rabbit_mq.event_publisher'
                    ]
                ]
            );
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->alias);
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (['command' => 'commands', 'event' => 'events'] as $messageType => $configurationKey) {
            if (!$mergedConfig[$configurationKey]['enabled']) {
                continue;
            }

            $loader->load($configurationKey . '.yml');
            $container->setAlias(
                'simple_bus.rabbit_mq.' . $messageType . '_producer',
                $mergedConfig[$configurationKey]['producer_service_id']
            );
        }
    }
}
