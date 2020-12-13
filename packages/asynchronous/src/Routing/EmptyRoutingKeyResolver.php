<?php

namespace SimpleBus\Asynchronous\Routing;

class EmptyRoutingKeyResolver implements RoutingKeyResolver
{
    /**
     * Always use an empty routing key.
     */
    public function resolveRoutingKeyFor(object $message): string
    {
        return '';
    }
}
