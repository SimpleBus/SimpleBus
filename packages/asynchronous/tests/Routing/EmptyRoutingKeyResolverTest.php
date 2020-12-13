<?php

namespace SimpleBus\Asynchronous\Tests\Routing;

use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Routing\EmptyRoutingKeyResolver;

/**
 * @internal
 * @coversNothing
 */
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
     * @return object|\PHPUnit\Framework\MockObject\MockObject
     */
    private function messageDummy()
    {
        return new \stdClass();
    }
}
