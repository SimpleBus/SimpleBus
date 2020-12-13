<?php

namespace SimpleBus\JMSSerializerBridge;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use SimpleBus\Serialization\ObjectSerializer;

class JMSSerializerObjectSerializer implements ObjectSerializer
{
    private SerializerInterface $serializer;

    private string $format;

    public function __construct(
        SerializerInterface $serializer,
        string $format
    ) {
        $this->serializer = $serializer;
        $this->format = $format;
    }

    public function serialize(object $object): string
    {
        $serializationContext = SerializationContext::create()
            ->setSerializeNull(true);

        return $this->serializer->serialize($object, $this->format, $serializationContext);
    }

    /**
     * @param class-string $type
     */
    public function deserialize(string $serializedObject, string $type): object
    {
        return $this->serializer->deserialize($serializedObject, $type, $this->format);
    }
}
