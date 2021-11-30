<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationReader;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEventSubscriberUsingPublicMethodAndUnion;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('annotation_reader', AnnotationReader::class);

    $services->set('auto_event_subscriber_using_public_method_and_union', AutoEventSubscriberUsingPublicMethodAndUnion::class)
        ->tag('event_subscriber', [
            'register_public_methods' => true,
        ]);

    $container->extension(
        'framework',
        [
            'secret' => 'secret',
        ]
    );

    $container->extension(
        'doctrine',
        [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'path' => ':memory:',
                'memory' => true,
            ],
            'orm' => [
                'entity_managers' => [
                    'default' => [
                        'connection' => 'default',
                        'mappings' => [
                            'test' => [
                                'type' => 'annotation',
                                'dir' => '%kernel.project_dir%/Entity/',
                                'prefix' => 'SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Entity',
                                'alias' => 'Test',
                                'is_bundle' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ]
    );
};
