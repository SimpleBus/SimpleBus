<?php

namespace SimpleBus\BernardBundleBridge\Tests\Routing;

use SimpleBus\BernardBundleBridge\Routing\DefaultQueueNameResolver;

class DefaultQueueNameResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getData
     *
     * @param object $message
     * @param string $producer
     */
    public function testResolveAdditionalPropertiesFor($message, $producer)
    {
        $props = (new DefaultQueueNameResolver())->resolveRoutingKeyFor($message);

        $this->assertEquals(['producer' => $producer], $props);
    }

    public function getData()
    {
        return [
            [new FooBar(), 'foo_bar'],
            [new FooBarCommand(), 'foo_bar_command'],
            [new FooBarEvent(), 'foo_bar_event'],
            [new FooBarCommandEvent(), 'foo_bar_command_event'],
        ];
    }
}

class FooBar
{
}

class FooBarCommand
{
}

class FooBarEvent
{
}

class FooBarCommandEvent
{
}
