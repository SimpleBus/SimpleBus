<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle;
use SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle;
use SimpleBus\SymfonyBridge\SimpleBusEventBusBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    private string $tempDir;

    public function __construct()
    {
        parent::__construct('test', true);

        $this->tempDir = __DIR__.'/temp';
    }

    /**
     * @return Bundle[]
     */
    public function registerBundles(): array
    {
        return [
            new SimpleBusCommandBusBundle(),
            new SimpleBusEventBusBundle(),
            new SimpleBusAsynchronousBundle(),
            new MonologBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config.yml');
    }

    public function getCacheDir(): string
    {
        return $this->tempDir.'/cache';
    }

    public function getLogDir(): string
    {
        return $this->tempDir.'/logs';
    }

    protected function getContainerClass(): string
    {
        return parent::getContainerClass().sha1(__NAMESPACE__);
    }
}
