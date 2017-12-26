<?php

namespace SimpleBus\BernardBundleBridge\Tests\Routing;

use PHPUnit\Framework\TestCase;
use SimpleBus\BernardBundleBridge\Routing\ClassBasedQueueNameResolver;

class ClassBasedQueueNameResolverTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider getData
     *
     * @param object $message
     * @param string $expected
     */
    public function it_should_resolve_a_queue_based_on_message_instance($message, $expected)
    {
        $queueName = (new ClassBasedQueueNameResolver())->resolveRoutingKeyFor($message);

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
