<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\Asynchronous\Message\Serializer\ObjectSerializer;

class NativeObjectSerializer implements ObjectSerializer
{
    public function serialize($object)
    {
        return serialize($object);
    }

    public function deserialize($serializedObject, $type)
    {
        return unserialize($serializedObject);
    }
}
