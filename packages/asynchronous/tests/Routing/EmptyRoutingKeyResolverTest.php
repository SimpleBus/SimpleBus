<?php

namespace SimpleBus\Asynchronous\Tests\Routing;

use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Routing\EmptyRoutingKeyResolver;

class EmptyRoutingKeyResolverTest extends TestCase
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
     * @return \PHPUnit_Framework_MockObject_MockObject|object
     */
    private function messageDummy()
    {
        return new \stdClass();
    }
}
