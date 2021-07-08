<?php

/**
 * Copyright (c) 2013-2017 Matthias Noback.
 */

namespace SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer\PHPUnit;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer\ServiceProvider;
use SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer\ServiceProvider\DoctrineDbalServiceProvider;
use SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer\ServiceProvider\DoctrineOrmServiceProvider;

trait TestCaseWithEntityManager
{
    use TestCaseWithServiceContainer;

    /**
     * Return the directories containing the entity classes that should be loaded.
     *
     * @return string[]
     */
    abstract protected function getEntityDirectories(): array;

    /**
     * @return ServiceProvider[]
     */
    protected function getServiceProviders(): array
    {
        return [
            new DoctrineDbalServiceProvider(new Schema()),
            new DoctrineOrmServiceProvider($this->getEntityDirectories()),
        ];
    }

    protected function getEntityManager(): EntityManager
    {
        return $this->container['doctrine_orm.entity_manager'];
    }

    protected function getEventManager(): EventManager
    {
        return $this->container['doctrine_dbal.event_manager'];
    }

    protected function getConnection(): Connection
    {
        return $this->container['doctrine_dbal.connection'];
    }
}
