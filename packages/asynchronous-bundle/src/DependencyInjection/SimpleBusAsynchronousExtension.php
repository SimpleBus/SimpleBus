<?php

namespace SimpleBus\AsynchronousBundle\DependencyInjection;

use LogicException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class SimpleBusAsynchronousExtension extends ConfigurableExtension
{
    private string $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param mixed[] $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration($this->alias);
    }

    /**
     * @param mixed[] $mergedConfig
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('asynchronous_serialization.php');
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

    private function requireBundle(string $bundleName, ContainerBuilder $container): void
    {
        $enabledBundles = (array) $container->getParameter('kernel.bundles');
        if (!isset($enabledBundles[$bundleName])) {
            throw new LogicException(sprintf('You need to enable "%s" as well', $bundleName));
        }
    }

    /**
     * @param mixed[] $config
     */
    private function loadAsynchronousCommandBus(array $config, ContainerBuilder $container, LoaderInterface $loader): void
    {
        $this->requireBundle('SimpleBusCommandBusBundle', $container);
        $loader->load('asynchronous_commands.php');

        $container->setAlias(
            'simple_bus.asynchronous.command_bus.command_name_resolver',
            'simple_bus.command_bus.command_name_resolver'
        );

        $container->setAlias(
            'simple_bus.asynchronous.command_publisher',
            $config['publisher_service_id']
        );

        if ($config['logging']['enabled']) {
            $loader->load('asynchronous_commands_logging.php');
        }
    }

    /**
     * @param mixed[] $config
     */
    private function loadAsynchronousEventBus(array $config, ContainerBuilder $container, LoaderInterface $loader): void
    {
        $this->requireBundle('SimpleBusEventBusBundle', $container);
        $loader->load('asynchronous_events.php');

        $container->setAlias(
            'simple_bus.asynchronous.event_bus.event_name_resolver',
            'simple_bus.event_bus.event_name_resolver'
        );

        $container->setAlias(
            'simple_bus.asynchronous.event_publisher',
            $config['publisher_service_id']
        );

        if ($config['logging']['enabled']) {
            $loader->load('asynchronous_events_logging.php');
        }

        $eventMiddleware = $config['strategy']['strategy_service_id'];

        // insert before the middleware that actually notifies a message subscriber of the message
        $container->getDefinition($eventMiddleware)->addTag('event_bus_middleware', ['priority' => 0]);
    }
}
