<?php

namespace SimpleBus\AsynchronousBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class SimpleBusAsynchronousExtension extends ConfigurableExtension
{
    /**
     * @var string
     */
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->alias);
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('serialization.yml');
        $container->setAlias(
            'simple_bus.asynchronous.message_serializer',
            $mergedConfig['message_serializer_service_id']
        );

        if ($mergedConfig['commands']['enabled']) {
            $this->requireBundle('SimpleBusCommandBusBundle', $container);
            $loader->load('asynchronous_commands.yml');

            $container->setAlias(
                'simple_bus.asynchronous.command_bus.command_name_resolver',
                'simple_bus.command_bus.command_name_resolver'
            );

            $container->setAlias(
                'simple_bus.asynchronous.command_publisher',
                $mergedConfig['commands']['publisher_service_id']
            );
        }

        if ($mergedConfig['events']['enabled']) {
            $this->requireBundle('SimpleBusEventBusBundle', $container);
            $loader->load('asynchronous_events.yml');

            $container->setAlias(
                'simple_bus.asynchronous.event_bus.event_name_resolver',
                'simple_bus.event_bus.event_name_resolver'
            );

            $container->setAlias(
                'simple_bus.asynchronous.event_publisher',
                $mergedConfig['events']['publisher_service_id']
            );
        }
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
