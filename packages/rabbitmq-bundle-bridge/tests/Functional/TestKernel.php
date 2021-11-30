<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use JMS\SerializerBundle\JMSSerializerBundle;
use OldSound\RabbitMqBundle\OldSoundRabbitMqBundle;
use SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle;
use SimpleBus\JMSSerializerBundleBridge\SimpleBusJMSSerializerBundleBridgeBundle;
use SimpleBus\RabbitMQBundleBridge\SimpleBusRabbitMQBundleBridgeBundle;
use SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle;
use SimpleBus\SymfonyBridge\SimpleBusEventBusBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
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
            new FrameworkBundle(),
            new JMSSerializerBundle(),
            new OldSoundRabbitMqBundle(),
            new SimpleBusAsynchronousBundle(),
            new SimpleBusCommandBusBundle(),
            new SimpleBusEventBusBundle(),
            new SimpleBusJMSSerializerBundleBridgeBundle(),
            new SimpleBusRabbitMQBundleBridgeBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config.php');
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
