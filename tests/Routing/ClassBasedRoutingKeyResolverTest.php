<?php

namespace SimpleBus\RabbitMQBundle\Tests\Routing;

use SimpleBus\RabbitMQBundle\Routing\ClassBasedRoutingKeyResolver;
use SimpleBus\RabbitMQBundle\Tests\Routing\Fixtures\MessageDummy;

class ClassBasedRoutingKeyResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_the_fqcn_with_dots_instead_of_backslashes()
    {
        $message = new MessageDummy();
        $resolver = new ClassBasedRoutingKeyResolver();

        $routingKey = $resolver->resolveRoutingKeyFor($message);

        $this->assertSame('SimpleBus.RabbitMQBundle.Tests.Routing.Fixtures.MessageDummy', $routingKey);
    }
}
