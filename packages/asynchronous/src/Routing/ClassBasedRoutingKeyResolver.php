<?php

namespace SimpleBus\Asynchronous\Routing;

class ClassBasedRoutingKeyResolver implements RoutingKeyResolver
{
    public function resolveRoutingKeyFor(object $message): string
    {
        return str_replace(
            '\\',
            '.',
            get_class($message)
        );
    }
}
