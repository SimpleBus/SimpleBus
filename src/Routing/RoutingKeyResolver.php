<?php

namespace SimpleBus\RabbitMQBundle\Routing;

use SimpleBus\Message\Message;

interface RoutingKeyResolver
{
    /**
     * Determine a routing key for AMQP messages containing a serialized version of this Message
     *
     * @param Message $message
     * @return string The routing key or empty string if no routing key needs to be used
     */
    public function resolveRoutingKeyFor(Message $message);
}
