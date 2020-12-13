<?php

namespace SimpleBus\Asynchronous\Tests\Routing;

use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Routing\ClassBasedRoutingKeyResolver;
use SimpleBus\Asynchronous\Tests\Routing\Fixtures\MessageDummy;

class ClassBasedRoutingKeyResolverTest extends TestCase
{
    /**
     * @test
     */
    public function itReturnsTheFqcnWithDotsInsteadOfBackslashes()
    {
        $message = new MessageDummy();
        $resolver = new ClassBasedRoutingKeyResolver();

        $routingKey = $resolver->resolveRoutingKeyFor($message);

        $this->assertSame('SimpleBus.Asynchronous.Tests.Routing.Fixtures.MessageDummy', $routingKey);
    }
}
