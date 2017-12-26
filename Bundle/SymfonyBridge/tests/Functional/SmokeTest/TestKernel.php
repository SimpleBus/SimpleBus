<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle;
use SimpleBus\SymfonyBridge\DoctrineOrmBridgeBundle;
use SimpleBus\SymfonyBridge\SimpleBusEventBusBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    private $tempDir = __DIR__ . '/temp';

    public function registerBundles()
    {
        return array(
            new DoctrineBundle(),
            new DoctrineOrmBridgeBundle(),
            new FrameworkBundle(),
            new MonologBundle(),
            new SimpleBusCommandBusBundle(),
            new SimpleBusEventBusBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/%s.yml', __DIR__, $this->environment));
    }

    public function getCacheDir()
    {
        return $this->tempDir . '/cache';
    }

    public function getLogDir()
    {
        return $this->tempDir . '/logs';
    }

    protected function getContainerClass()
    {
        return parent::getContainerClass() . sha1(__NAMESPACE__);
    }
}
