<?php

namespace SimpleBus\BernardBundleBridge;

use Bernard\Envelope;
use Bernard\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BernardRouter implements Router
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function map(Envelope $envelope)
    {
        /* @var \Bernard\Message\DefaultMessage $message */
        $message = $envelope->getMessage();

        $serviceId = sprintf('simple_bus.bernard_bundle_bridge.%s_consumer', $message->get('type'));

        return $this->container->get($serviceId);
    }
}
