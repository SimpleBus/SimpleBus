<?php

namespace SimpleBus\BernardBundleBridge\Tests\Routing;

use SimpleBus\BernardBundleBridge\Routing\FixedQueueNameResolver;
use stdClass;

class FixedQueueNameResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveRoutingKeyFor()
    {
        $queueName = (new FixedQueueNameResolver('fixed-queue-name'))->resolveRoutingKeyFor(new stdClass());

        $this->assertEquals('fixed-queue-name', $queueName);
    }
}
