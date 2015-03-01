<?php

namespace SimpleBus\JMSSerializerBundle\DependencyInjection;

use SimpleBus\JMSSerializerBridge\SerializerMetadata;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class SimpleBusJMSSerializerExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
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
                            'namespace_prefix' => SerializerMetadata::namespacePrefix()
                        ]
                    ]
                ]
            ]
        );

        $container->prependExtensionConfig(
            'simple_bus_asynchronous',
            [
                'message_serializer_service_id' => 'simple_bus.jms_serializer.message_serializer'
            ]
        );
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
