<?php

/**
 * Copyright (c) 2013-2017 Matthias Noback.
 */

namespace SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer;

use Pimple\ServiceProviderInterface;

/**
 * Implement this interface to extend service containers.
 *
 * A service provider is allowed to register services to the service container.
 */
interface ServiceProvider extends ServiceProviderInterface
{
    /**
     * Will be called before each test method in a test class (setUp).
     *
     * Use the provided ServiceContainerInterface instance to initialize services.
     *
     * For example:
     *
     *   $serviceContainer['database']->create();
     *   $serviceContainer['schema']->create($serviceContainer['database']);
     */
    public function setUp(ServiceContainer $serviceContainer): void;

    /**
     * Will be called after each test method in a test class (tearDown).
     *
     * Use it to reset services with state, or remove other traces of the previous test.
     *
     *   $serviceContainer['database']->drop();
     */
    public function tearDown(ServiceContainer $serviceContainer): void;
}
