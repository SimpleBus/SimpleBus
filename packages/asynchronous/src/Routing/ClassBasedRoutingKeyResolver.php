<?php

namespace SimpleBus\Asynchronous\Routing;

class ClassBasedRoutingKeyResolver implements RoutingKeyResolver
{
    public function resolveRoutingKeyFor($message)
    {
        return str_replace(
            '\\',
            '.',
            get_class($message)
        );
    }
}
