<?php

namespace SimpleBus\BernardBundleBridge\Routing;

use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;

class MappedQueueNameResolver implements RoutingKeyResolver
{
    /**
     * @var array
     */
    private $map;

    /**
     * @var string
     */
    private $default;

    /**
     * @param array  $map
     * @param string $default
     */
    public function __construct(array $map, $default)
    {
        $this->map = $map;
        $this->default = $default;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRoutingKeyFor($message)
    {
        $class = get_class($message);

        if (!isset($this->map[$class])) {
            return $this->default;
        }

        return $this->map[$class];
    }
}
