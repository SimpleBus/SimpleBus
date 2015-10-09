<?php

namespace SimpleBus\BernardBundleBridge\Routing;

use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;

class FixedQueueNameResolver implements RoutingKeyResolver
{
    /**
     * @var string
     */
    private $queue;

    /**
     * @param string $queue
     */
    public function __construct($queue)
    {
        $this->queue = $queue;
    }

    /**
     * @inheritdoc
     */
    public function resolveRoutingKeyFor($message)
    {
        return $this->queue;
    }
}
