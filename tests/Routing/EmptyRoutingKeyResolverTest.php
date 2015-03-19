<?php

namespace SimpleBus\Asynchronous\Tests\Routing;

use SimpleBus\Asynchronous\Routing\EmptyRoutingKeyResolver;
use SimpleBus\Message\Message;

class EmptyRoutingKeyResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_an_empty_routing_key()
    {
        $resolver = new EmptyRoutingKeyResolver();
        $this->assertSame('', $resolver->resolveRoutingKeyFor($this->messageDummy()));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Message
     */
    private function messageDummy()
    {
        return $this->getMock('SimpleBus\Message\Message');
    }
}
