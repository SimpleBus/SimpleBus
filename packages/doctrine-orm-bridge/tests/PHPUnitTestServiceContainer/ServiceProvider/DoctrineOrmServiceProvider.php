<?php

/**
 * Copyright (c) 2013-2017 Matthias Noback.
 */

namespace SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer\ServiceProvider;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Pimple\Container;
use SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer\ServiceContainer;
use SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer\ServiceProvider;

class DoctrineOrmServiceProvider implements ServiceProvider
{
    /**
     * @var string[]
     */
    private array $entityDirectories;

    /**
     * @param string[] $entityDirectories
     */
    public function __construct(array $entityDirectories = [])
    {
        $this->entityDirectories = $entityDirectories;
    }

    public function setUp(ServiceContainer $serviceContainer): void
    {
        $this->createSchema(
            $serviceContainer['doctrine_orm.schema_tool'],
            $serviceContainer['doctrine_orm.entity_manager']
        );
    }

    public function tearDown(ServiceContainer $serviceContainer): void
    {
        $this->dropSchema(
            $serviceContainer['doctrine_orm.schema_tool'],
            $serviceContainer['doctrine_orm.entity_manager']
        );
    }

    public function register(Container $serviceContainer): void
    {
        $serviceContainer['doctrine_orm.entity_directories'] = $this->entityDirectories;
        $serviceContainer['doctrine_orm.development_mode'] = true;
        $serviceContainer['doctrine_orm.proxy_dir'] = sys_get_temp_dir();

        $serviceContainer['doctrine_orm.entity_manager'] = function (ServiceContainer $serviceContainer) {
            return EntityManager::create(
                $serviceContainer['doctrine_dbal.connection'],
                $serviceContainer['doctrine_orm.configuration'],
                $serviceContainer['doctrine_dbal.event_manager']
            );
        };

        $serviceContainer['doctrine_orm.configuration'] = function (ServiceContainer $serviceContainer) {
            return Setup::createAnnotationMetadataConfiguration(
                $serviceContainer['doctrine_orm.entity_directories'],
                $serviceContainer['doctrine_orm.development_mode'],
                $serviceContainer['doctrine_orm.proxy_dir']
            );
        };

        $serviceContainer['doctrine_orm.schema_tool'] = function (ServiceContainer $serviceContainer) {
            return new SchemaTool($serviceContainer['doctrine_orm.entity_manager']);
        };
    }

    private function createSchema(SchemaTool $schemaTool, EntityManager $entityManager): void
    {
        $schemaTool->createSchema($this->getClassMetadatas($entityManager));
    }

    private function dropSchema(SchemaTool $schemaTool, EntityManager $entityManager): void
    {
        $schemaTool->dropSchema($this->getClassMetadatas($entityManager));
    }

    /**
     * @return array<int, ClassMetadata<object>>
     */
    private function getClassMetadatas(EntityManager $entityManager): array
    {
        return $entityManager->getMetadataFactory()->getAllMetadata();
    }
}
