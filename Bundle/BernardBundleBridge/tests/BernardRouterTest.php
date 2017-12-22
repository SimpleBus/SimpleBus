<?php

namespace SimpleBus\BernardBundleBridge\Tests;

use Bernard\Envelope;
use Bernard\Message\PlainMessage;
use SimpleBus\BernardBundleBridge\BernardRouter;

class BernardRouterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_should_retrieve_a_consumer_for_type()
    {
        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [$this->equalTo('simple_bus.bernard_bundle_bridge.foo_consumer')],
                [$this->equalTo('simple_bus.bernard_bundle_bridge.bar_consumer')]
            )
        ;

        $router = new BernardRouter($container);

        $router->map(new Envelope(new PlainMessage('name', ['type' => 'foo'])));
        $router->map(new Envelope(new PlainMessage('name', ['type' => 'bar'])));
    }
}
