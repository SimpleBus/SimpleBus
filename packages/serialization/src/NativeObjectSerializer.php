<?php

namespace SimpleBus\Serialization;

class NativeObjectSerializer implements ObjectSerializer
{
    /**
     * Serialize the given object using the native `serialize()` function
     *
     * @{inheritdoc}
     */
    public function serialize($object)
    {
        return serialize($object);
    }

    /**
     * Deserialize the given object using the native `unserialize()` function
     *
     * @{inheritdoc}
     */
    public function deserialize($serializedObject, $type)
    {
        $deserializedObject = unserialize($serializedObject);

        if (!($deserializedObject instanceof $type)) {
            throw new \LogicException(
                sprintf(
                    'Unserialized object was expected to be of type "%s"',
                    $type
                )
            );
        }

        return $deserializedObject;
    }
}
