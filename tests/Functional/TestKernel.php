<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle;
use SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle;
use SimpleBus\SymfonyBridge\SimpleBusEventBusBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    private $tempDir;

    public function __construct($tempDir)
    {
        $this->tempDir = __DIR__ . '/temp';
    }

    public function registerBundles()
    {
        return [
            new SimpleBusCommandBusBundle(),
            new SimpleBusEventBusBundle(),
            new SimpleBusAsynchronousBundle()
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');
    }

    public function getCacheDir()
    {
        return $this->tempDir . '/cache';
    }

    public function getLogDir()
    {
        return $this->tempDir . '/logs';
    }
}
