<?php

namespace SimpleBus\Asynchronous\Routing;

interface RoutingKeyResolver
{
    /**
     * Determine a routing key for messages containing a serialized version of this message.
     *
     * @return string The routing key or empty string if no routing key needs to be used
     */
    public function resolveRoutingKeyFor(object $message): string;
}
