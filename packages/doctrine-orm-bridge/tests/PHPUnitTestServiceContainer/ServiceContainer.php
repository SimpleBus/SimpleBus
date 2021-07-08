<?php

/**
 * Copyright (c) 2013-2017 Matthias Noback.
 */

namespace SimpleBus\DoctrineORMBridge\Tests\PHPUnitTestServiceContainer;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ServiceContainer extends Container
{
    /**
     * @var ServiceProvider[]
     */
    private array $serviceProviders = [];

    public function setUp(): void
    {
        foreach ($this->serviceProviders as $serviceProvider) {
            $serviceProvider->setUp($this);
        }
    }

    public function tearDown(): void
    {
        foreach ($this->serviceProviders as $serviceProvider) {
            $serviceProvider->tearDown($this);
        }
    }

    /**
     * @param array<mixed> $values
     */
    public function register(ServiceProviderInterface $provider, array $values = []): self
    {
        assert($provider instanceof ServiceProvider);

        $this->serviceProviders[] = $provider;

        parent::register($provider, $values);

        return $this;
    }
}
