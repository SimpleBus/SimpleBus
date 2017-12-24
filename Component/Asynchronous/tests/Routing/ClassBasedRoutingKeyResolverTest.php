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
    public function it_returns_the_fqcn_with_dots_instead_of_backslashes()
    {
        $message = new MessageDummy();
        $resolver = new ClassBasedRoutingKeyResolver();

        $routingKey = $resolver->resolveRoutingKeyFor($message);

        $this->assertSame('SimpleBus.Asynchronous.Tests.Routing.Fixtures.MessageDummy', $routingKey);
    }
}
