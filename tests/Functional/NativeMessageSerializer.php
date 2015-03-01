<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\Asynchronous\Message\Serializer\MessageSerializer;
use SimpleBus\Message\Message;

class NativeMessageSerializer implements MessageSerializer
{
    public function serialize(Message $message)
    {
        return serialize($message);
    }

    public function deserialize($serializedMessageEnvelope)
    {
        return unserialize($serializedMessageEnvelope);
    }
}
