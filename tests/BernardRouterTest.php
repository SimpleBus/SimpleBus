<?php

namespace SimpleBus\BernardBundleBridge\Tests;

use Bernard\Envelope;
use Bernard\Message\DefaultMessage;
use SimpleBus\BernardBundleBridge\BernardRouter;

class BernardRouterTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [$this->equalTo('simple_bus.bernard_bundle_bridge.foo_consumer')],
                [$this->equalTo('simple_bus.bernard_bundle_bridge.bar_consumer')]
            )
        ;

        $router = new BernardRouter($container);

        $router->map(new Envelope(new DefaultMessage('name', ['type' => 'foo'])));
        $router->map(new Envelope(new DefaultMessage('name', ['type' => 'bar'])));
    }
}
