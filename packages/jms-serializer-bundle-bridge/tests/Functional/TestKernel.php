<?php

namespace SimpleBus\JMSSerializerBundleBridge\Tests\Functional;

use JMS\SerializerBundle\JMSSerializerBundle;
use SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle;
use SimpleBus\JMSSerializerBundleBridge\SimpleBusJMSSerializerBundleBridgeBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    private string $tempDir = __DIR__.'/temp';

    /**
     * @return Bundle[]
     */
    public function registerBundles(): array
    {
        return [
            new SimpleBusAsynchronousBundle(),
            new JMSSerializerBundle(),
            new SimpleBusJMSSerializerBundleBridgeBundle(),
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
