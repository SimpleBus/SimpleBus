<?php

namespace SimpleBus\BernardBundleBridge\Routing;

use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;

class DefaultQueueNameResolver implements RoutingKeyResolver
{
    public function resolveRoutingKeyFor($message)
    {
        $name = self::tableize(substr(get_class($message), strrpos(get_class($message), '\\') + 1));

        if (substr($name, -8) === '_command') {
            $name = substr($name, 0, strlen($name) - 8);
        } elseif (substr($name, -6) === '_event') {
            $name = substr($name, 0, strlen($name) - 6);
        }

        return $name;
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
