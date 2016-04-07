<?php

namespace SimpleBus\AsynchronousBundle\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
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
            'simple_bus.asynchronous.object_serializer',
            $mergedConfig['object_serializer_service_id']
        );

        if ($mergedConfig['commands']['enabled']) {
            $this->loadAsynchronousCommandBus($mergedConfig['commands'], $container, $loader);
        }

        if ($mergedConfig['events']['enabled']) {
            $this->loadAsynchronousEventBus($mergedConfig['events'], $container, $loader);
        }
    }

    private function requireBundle($bundleName, ContainerBuilder $container)
    {
        $enabledBundles = $container->getParameter('kernel.bundles');
        if (!isset($enabledBundles[$bundleName])) {
            throw new \LogicException(sprintf('You need to enable "%s" as well', $bundleName));
        }
    }

    private function loadAsynchronousCommandBus(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        $this->requireBundle('SimpleBusCommandBusBundle', $container);
        $loader->load('asynchronous_commands.yml');

        $container->setAlias(
            'simple_bus.asynchronous.command_bus.command_name_resolver',
            'simple_bus.command_bus.command_name_resolver'
        );

        $container->setAlias(
            'simple_bus.asynchronous.command_publisher',
            $config['publisher_service_id']
        );

        if ($config['logging']['enabled']) {
            $loader->load('asynchronous_commands_logging.yml');
        }
    }

    private function loadAsynchronousEventBus(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        $this->requireBundle('SimpleBusEventBusBundle', $container);
        $loader->load('asynchronous_events.yml');

        $container->setAlias(
            'simple_bus.asynchronous.event_bus.event_name_resolver',
            'simple_bus.event_bus.event_name_resolver'
        );

        $container->setAlias(
            'simple_bus.asynchronous.event_publisher',
            $config['publisher_service_id']
        );

        if ($config['logging']['enabled']) {
            $loader->load('asynchronous_events_logging.yml');
        }


        if ($config['strategy'] === 'always') {
            $eventMiddleware = 'simple_bus.asynchronous.always_publishes_messages_middleware';
        } else {
            $eventMiddleware = 'simple_bus.asynchronous.publishes_predefined_messages_middleware';
        }

        // insert before the middleware that actually notifies a message subscriber of the message
        $container->getDefinition($eventMiddleware)->addTag('event_bus_middleware', ['priority' => 0]);
    }
}
