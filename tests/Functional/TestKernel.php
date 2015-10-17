<?php

namespace SimpleBus\BernardBundleBridge\Tests\Functional;

use Bernard\BernardBundle\BernardBundle;
use SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle;
use SimpleBus\BernardBundleBridge\SimpleBusBernardBundleBridgeBundle;
use SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle;
use SimpleBus\SymfonyBridge\SimpleBusEventBusBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    private $tempDir;

    public function __construct()
    {
        parent::__construct('test', true);

        $this->tempDir = __DIR__.'/temp';
    }

    public function getCacheDir()
    {
        return $this->tempDir.'/cache';
    }

    public function getLogDir()
    {
        return $this->tempDir.'/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new BernardBundle(),

            // Simple bus
            new SimpleBusCommandBusBundle(),
            new SimpleBusEventBusBundle(),
            new SimpleBusAsynchronousBundle(),
            new SimpleBusBernardBundleBridgeBundle(),
        ];
    }
}
