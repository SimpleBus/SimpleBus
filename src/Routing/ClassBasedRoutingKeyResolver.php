<?php

namespace SimpleBus\Asynchronous\Routing;

use SimpleBus\Message\Message;

class ClassBasedRoutingKeyResolver implements RoutingKeyResolver
{
    public function resolveRoutingKeyFor(Message $message)
    {
        return str_replace(
            '\\',
            '.',
            get_class($message)
        );
    }
}
