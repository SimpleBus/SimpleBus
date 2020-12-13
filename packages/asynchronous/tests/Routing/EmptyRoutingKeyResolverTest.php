<?php

namespace SimpleBus\Asynchronous\Tests\Routing;

use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Routing\EmptyRoutingKeyResolver;

class EmptyRoutingKeyResolverTest extends TestCase
{
    /**
     * @test
     */
    public function itReturnsAnEmptyRoutingKey()
    {
        $resolver = new EmptyRoutingKeyResolver();
        $this->assertSame('', $resolver->resolveRoutingKeyFor($this->messageDummy()));
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|object
     */
    private function messageDummy()
    {
        return new \stdClass();
    }
}
