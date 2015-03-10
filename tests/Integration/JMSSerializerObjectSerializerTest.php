<?php

namespace SimpleBus\JMSSerializerBridge\Tests\Integration;

use JMS\Serializer\SerializerBuilder;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\JMSSerializerBridge\JMSSerializerObjectSerializer;
use SimpleBus\JMSSerializerBridge\SerializerMetadata;

class JMSSerializerObjectSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_serializes_and_deserializes_messages()
    {
        $format = 'json';
        $jmsSerializer = SerializerBuilder::create()
            ->addMetadataDir(SerializerMetadata::directory(), SerializerMetadata::namespacePrefix())
            ->build();

        $originalEnvelope = DefaultEnvelope::forSerializedMessage(
            'SimpleBus\JMSSerializerBridge\Tests\Integration\SampleMessage',
            '{}'
        );

        $objectSerializer = new JMSSerializerObjectSerializer($jmsSerializer, $format);
        $serializedEnvelope = $objectSerializer->serialize($originalEnvelope);
        $deserializedMessage = $objectSerializer->deserialize($serializedEnvelope, get_class($originalEnvelope));
        $this->assertEquals($deserializedMessage, $originalEnvelope);
    }
}
