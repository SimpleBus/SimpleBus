<?php

namespace SimpleBus\Asynchronous\Properties;

interface AdditionalPropertiesResolver
{
    /**
     * Determine additional properties for messages containing a serialized version of this message.
     *
     * @return mixed[] The array of additional properties or empty array
     */
    public function resolveAdditionalPropertiesFor(object $message): array;
}
