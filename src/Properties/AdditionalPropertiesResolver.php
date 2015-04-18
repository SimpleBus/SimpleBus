<?php

namespace SimpleBus\Asynchronous\Properties;

use SimpleBus\Message\Message;

interface AdditionalPropertiesResolver
{
    /**
     * Determine additional properties for messages containing a serialized version of this Message
     *
     * @param Message $message
     * @return array The array of additional properties or empty array
     */
    public function resolveAdditionalPropertiesFor(Message $message);
}
