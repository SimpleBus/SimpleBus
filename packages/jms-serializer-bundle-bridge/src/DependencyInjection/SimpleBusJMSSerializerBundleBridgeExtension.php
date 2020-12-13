<?php

namespace SimpleBus\JMSSerializerBundleBridge\DependencyInjection;

use LogicException;
use SimpleBus\JMSSerializerBridge\SerializerMetadata;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SimpleBusJMSSerializerBundleBridgeExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param mixed[] $config
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->requireBundle('JMSSerializerBundle', $container);
        $this->requireBundle('SimpleBusAsynchronousBundle', $container);

        $container->prependExtensionConfig(
            'jms_serializer',
            [
                'metadata' => [
                    'directories' => [
                        'AsynchronousEvents' => [
                            'path' => SerializerMetadata::directory(),
                            'namespace_prefix' => SerializerMetadata::namespacePrefix(),
                        ],
                    ],
                ],
            ]
        );

        $container->prependExtensionConfig(
            'simple_bus_asynchronous',
            [
                'object_serializer_service_id' => 'simple_bus.jms_serializer.object_serializer',
            ]
        );
    }

    private function requireBundle(string $bundleName, ContainerBuilder $container): void
    {
        $enabledBundles = $container->getParameter('kernel.bundles');
        if (!isset($enabledBundles[$bundleName])) {
            throw new LogicException(sprintf('You need to enable "%s" as well', $bundleName));
        }
    }
}
