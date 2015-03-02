<?php

namespace SimpleBus\RabbitMQBundle\Tests\Functional;

use JMS\SerializerBundle\JMSSerializerBundle;
use OldSound\RabbitMqBundle\OldSoundRabbitMqBundle;
use SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle;
use SimpleBus\JMSSerializerBundle\SimpleBusJMSSerializerBundle;
use SimpleBus\RabbitMQBundle\SimpleBusRabbitMQBundle;
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
        $this->tempDir = __DIR__ . '/temp';
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new OldSoundRabbitMqBundle(),
            new SimpleBusCommandBusBundle(),
            new SimpleBusEventBusBundle(),
            new SimpleBusAsynchronousBundle(),
            new SimpleBusRabbitMQBundle(),
            new JMSSerializerBundle(),
            new SimpleBusJMSSerializerBundle()
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
