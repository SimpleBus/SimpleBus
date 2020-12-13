<?php

namespace SimpleBus\JMSSerializerBridge\Tests\Integration;

use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use SimpleBus\JMSSerializerBridge\JMSSerializerObjectSerializer;
use SimpleBus\JMSSerializerBridge\SerializerMetadata;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;

/**
 * @internal
 * @coversNothing
 */
class JMSSerializerObjectSerializerTest extends TestCase
{
    /**
     * @test
     */
    public function itSerializesAndDeserializesMessages(): void
    {
        $format = 'json';
        $jmsSerializer = SerializerBuilder::create()
            ->addMetadataDir(SerializerMetadata::directory(), SerializerMetadata::namespacePrefix())
            ->build();

        $originalEnvelope = DefaultEnvelope::forSerializedMessage(
            SampleMessage::class,
            '{}'
        );

        $objectSerializer = new JMSSerializerObjectSerializer($jmsSerializer, $format);
        $serializedEnvelope = $objectSerializer->serialize($originalEnvelope);
        $deserializedMessage = $objectSerializer->deserialize($serializedEnvelope, get_class($originalEnvelope));
        $this->assertEquals($originalEnvelope, $deserializedMessage);
    }
}
