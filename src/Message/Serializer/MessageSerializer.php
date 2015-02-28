<?php

namespace SimpleBus\Asynchronous\Message\Serializer;

use SimpleBus\Message\Message;

interface MessageSerializer
{
    /**
     * Serialize a Message to a string representation of a MessageEnvelope object for that Message
     *
     * @param Message $message
     * @return string
     */
    public function serialize(Message $message);

    /**
     * Extract a Message from a serialized string representation of its MessageEnvelope object
     *
     * @param string $serializedMessageEnvelope
     * @return Message
     */
    public function deserialize($serializedMessageEnvelope);
}
