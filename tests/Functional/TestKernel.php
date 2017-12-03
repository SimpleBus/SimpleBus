<?php

namespace SimpleBus\JMSSerializerBundleBridge\Tests\Functional;

use JMS\SerializerBundle\JMSSerializerBundle;
use SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle;
use SimpleBus\JMSSerializerBundleBridge\SimpleBusJMSSerializerBundleBridgeBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    private $tempDir = __DIR__ . '/temp';

    public function registerBundles()
    {
        return [
            new SimpleBusAsynchronousBundle(),
            new JMSSerializerBundle(),
            new SimpleBusJMSSerializerBundleBridgeBundle()
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
