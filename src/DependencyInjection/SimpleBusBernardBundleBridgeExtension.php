<?php

namespace SimpleBus\BernardBundleBridge\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class SimpleBusBernardBundleBridgeExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->alias);
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->requireBundle('SimpleBusAsynchronousBundle', $container);
        $this->requireBundle('SimpleBusJMSSerializerBundleBridgeBundle', $container);
        $this->requireBundle('BernardBundle', $container);

        $container->prependExtensionConfig('simple_bus_asynchronous', [
            'commands' => [
                'publisher_service_id' => 'simple_bus.bernard_bundle_bridge.command_publisher',
            ],
            'events' => [
                'publisher_service_id' => 'simple_bus.bernard_bundle_bridge.event_publisher',
            ],
        ]);

        $config = $container->getExtensionConfig($this->getAlias());
        $merged = $this->processConfiguration($this->getConfiguration($config, $container), $config);

        if (!empty($merged['encryption']['enabled'])) {
            $container->prependExtensionConfig('simple_bus_asynchronous', [
                'object_serializer_service_id' => 'simple_bus.bernard_bundle_bridge.encrypted_serializer',
            ]);
        }
    }

    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($container->getParameter('kernel.debug')) {
            $loader->load('debug.xml');
        }

        $this->configureQueueResolver($config, $container);

        if (!empty($config['logger'])) {
            $container
                ->getDefinition('simple_bus.bernard_bundle_bridge.listener.logger')
                ->replaceArgument(0, new Reference($config['logger']))
                ->setAbstract(false)
            ;
        }

        if (!empty($config['encryption']['enabled'])) {
            $loader->load('encryption.xml');

            $this->configureEncryption($config['encryption'], $container);
        }
    }

    private function configureQueueResolver(array $config, ContainerBuilder $container)
    {
        if (in_array($config['queue_name_resolver'], ['default', 'mapped'])) {
            $serviceId = sprintf('simple_bus.bernard_bundle_bridge.routing.%s_queue_name_resolver', $config['queue_name_resolver']);
        } else {
            $serviceId = $config['queue_name_resolver'];
        }

        if ($config['queue_name_resolver'] === 'mapped') {
            $container->getDefinition($serviceId)->replaceArgument(0, $config['queues_map']);
        }

        $container->setAlias('simple_bus.bernard_bundle_bridge.routing.queue_name_resolver', $serviceId);
    }

    private function configureEncryption(array $config, ContainerBuilder $container)
    {
        if ($config['encrypter'] === 'nelmio') {
            $container
                ->getDefinition('simple_bus.bernard_bundle_bridge.encrypter.nelmio')
                ->setAbstract(false)
                ->setArguments([
                    $config['secret'],
                    $config['algorithm'],
                ])
            ;
            $container->setAlias('simple_bus.bernard_bundle_bridge.encrypter', 'simple_bus.bernard_bundle_bridge.encrypter.nelmio');
        } else {
            $container->setAlias('simple_bus.bernard_bundle_bridge.encrypter', $config['encrypter']);
        }
    }

    private function requireBundle($bundleName, ContainerBuilder $container)
    {
        if (!isset($container->getParameter('kernel.bundles')[$bundleName])) {
            throw new \LogicException(sprintf('You need to enable "%s" as well', $bundleName));
        }
    }
}
