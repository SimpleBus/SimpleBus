<?php

namespace SimpleBus\Asynchronous\Routing;

use SimpleBus\Message\Message;

class EmptyRoutingKeyResolver implements RoutingKeyResolver
{
    /**
     * Always use an empty routing key
     *
     * @{inheritdoc}
     */
    public function resolveRoutingKeyFor(Message $message)
    {
        return '';
    }
}
