<?php

namespace SimpleBus\BernardBundleBridge\Routing;

use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;

class ClassBasedQueueNameResolver implements RoutingKeyResolver
{
    public function resolveRoutingKeyFor($message)
    {
        return self::tableize(substr(get_class($message), strrpos(get_class($message), '\\') + 1));
    }

    /**
     * Taken from Doctrine\Common\Inflector.
     *
     * @param string $word
     *
     * @return string
     */
    private static function tableize($word)
    {
        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $word));
    }
}
