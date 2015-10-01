<?php

namespace SimpleBus\BernardBundleBridge\Routing;

use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;

class MappedQueueNameResolver implements RoutingKeyResolver
{
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function resolveRoutingKeyFor($message)
    {
        $class = get_class($message);

        if (!isset($this->map[$class])) {
            throw new \RuntimeException(sprintf('Unable to detect queue for message of type %s.', $class));
        }

        return $this->map[$class];
    }
}
