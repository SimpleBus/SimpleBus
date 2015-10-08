<?php

namespace SimpleBus\BernardBundleBridge\Tests\Routing;

use SimpleBus\BernardBundleBridge\Routing\MappedQueueNameResolver;

class MappedQueueNameResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveRoutingKeyFor()
    {
        $resolver = new MappedQueueNameResolver([
            __NAMESPACE__.'\\FooBar' => 'foo_queue',
            __NAMESPACE__.'\\FooBarCommand' => 'foo_bar_queue',
            __NAMESPACE__.'\\FooBarEvent' => 'foo_bar_event_queue',
            __NAMESPACE__.'\\FooBarCommandEvent' => 'foo_bar_command_queue',
        ], 'my-fallback-queue');

        $this->assertEquals('foo_queue', $resolver->resolveRoutingKeyFor(new FooBar()));
        $this->assertEquals('foo_bar_queue', $resolver->resolveRoutingKeyFor(new FooBarCommand()));
        $this->assertEquals('foo_bar_event_queue', $resolver->resolveRoutingKeyFor(new FooBarEvent()));
        $this->assertEquals('foo_bar_command_queue', $resolver->resolveRoutingKeyFor(new FooBarCommandEvent()));
        $this->assertEquals('my-fallback-queue', $resolver->resolveRoutingKeyFor(new \stdClass));
    }
}

class Baz
{
}
