<?php

namespace SimpleBus\Serialization;

interface ObjectSerializer
{
    /**
     * Serialize the given object as a deserializable string
     *
     * @param object $object
     * @return string
     */
    public function serialize($object);

    /**
     * Deserialize the given serialized object into an actual object of the given type
     *
     * @param string $serializedObject
     * @param string $type
     * @return object
     */
    public function deserialize($serializedObject, $type);
}
