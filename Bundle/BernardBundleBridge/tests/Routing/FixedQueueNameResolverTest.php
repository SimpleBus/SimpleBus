<?php

namespace SimpleBus\BernardBundleBridge\Tests\Routing;

use SimpleBus\BernardBundleBridge\Routing\FixedQueueNameResolver;
use stdClass;

/**
 * @group BernardBundleBridge
 */
class FixedQueueNameResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_should_resolve_to_a_fixed_queued_name()
    {
        $queueName = (new FixedQueueNameResolver('fixed-queue-name'))->resolveRoutingKeyFor(new stdClass());

        $this->assertEquals('fixed-queue-name', $queueName);
    }
}
