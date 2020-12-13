<?php

namespace SimpleBus\Asynchronous\Tests\Routing;

use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Routing\EmptyRoutingKeyResolver;
use stdClass;

/**
 * @internal
 * @coversNothing
 */
class EmptyRoutingKeyResolverTest extends TestCase
{
    /**
     * @test
     */
    public function itReturnsAnEmptyRoutingKey(): void
    {
        $resolver = new EmptyRoutingKeyResolver();
        $this->assertSame('', $resolver->resolveRoutingKeyFor($this->messageDummy()));
    }

    private function messageDummy(): stdClass
    {
        return new stdClass();
    }
}
