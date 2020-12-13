<?php

namespace SimpleBus\Serialization;

interface ObjectSerializer
{
    /**
     * Serialize the given object as a deserializable string.
     */
    public function serialize(object $object): string;

    /**
     * Deserialize the given serialized object into an actual object of the given type.
     *
     * @param class-string $type
     */
    public function deserialize(string $serializedObject, string $type): object;
}
