<?php

namespace SimpleBus\BernardBundleBridge\Tests\Routing;

use SimpleBus\BernardBundleBridge\Routing\DefaultQueueNameResolver;

class DefaultQueueNameResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getData
     *
     * @param object $message
     * @param string $expected
     */
    public function testResolveRoutingKeyFor($message, $expected)
    {
        $queueName = (new DefaultQueueNameResolver())->resolveRoutingKeyFor($message);

        $this->assertEquals($expected, $queueName);
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
