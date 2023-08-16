<?php

namespace SimpleBus\Serialization;

use LogicException;

class NativeObjectSerializer implements ObjectSerializer
{
    /**
     * Serialize the given object using the native `serialize()` function.
     */
    public function serialize(object $object): string
    {
        return serialize($object);
    }

    /**
     * Deserialize the given object using the native `unserialize()` function.
     *
     * @param class-string $type
     */
    public function deserialize(string $serializedObject, string $type): object
    {
        $deserializedObject = unserialize($serializedObject);

        if (!$deserializedObject instanceof $type) {
            throw new LogicException(sprintf('Unserialized object was expected to be of type "%s"', $type));
        }

        return $deserializedObject;
    }
}
