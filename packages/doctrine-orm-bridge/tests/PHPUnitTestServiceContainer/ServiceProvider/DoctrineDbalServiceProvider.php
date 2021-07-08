<?php

/**
 * Copyright (c) 2013-2017 Matthias Noback.
 */

namespace SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer\ServiceProvider;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Pimple\Container;
use SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer\ServiceContainer;
use SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer\ServiceProvider;

final class DoctrineDbalServiceProvider implements ServiceProvider
{
    private Schema $schema;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function setUp(ServiceContainer $serviceContainer): void
    {
        $this->createSchema($serviceContainer['doctrine_dbal.connection'], $serviceContainer['doctrine_dbal.schema']);
    }

    public function tearDown(ServiceContainer $serviceContainer): void
    {
        $this->closeConnection($serviceContainer['doctrine_dbal.connection']);
    }

    public function register(Container $serviceContainer): void
    {
        $serviceContainer['doctrine_dbal.connection_configuration'] = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        $serviceContainer['doctrine_dbal.event_manager'] = function () {
            return new EventManager();
        };

        $serviceContainer['doctrine_dbal.connection'] = function (ServiceContainer $serviceContainer) {
            return DriverManager::getConnection(
                $serviceContainer['doctrine_dbal.connection_configuration'],
                null,
                $serviceContainer['doctrine_dbal.event_manager']
            );
        };

        $serviceContainer['doctrine_dbal.schema'] = $this->schema;
    }

    private function createSchema(Connection $connection, Schema $schema): void
    {
        foreach ($schema->toSql($connection->getDatabasePlatform()) as $sql) {
            $connection->exec($sql);
        }
    }

    private function closeConnection(Connection $connection): void
    {
        $connection->close();
    }
}
