<?php

namespace SimpleBus\Serialization\Tests;

use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\NativeObjectSerializer;
use SimpleBus\Serialization\Tests\Fixtures\AnotherDummyMessage;

class NativeObjectSerializerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_can_serialize_a_default_message_envelope_with_a_serialized_message()
    {
        $envelope = DefaultEnvelope::forSerializedMessage(
            'SimpleBus\Serialization\Tests\Fixtures\DummyMessage',
            'serialized message'
        );
        $serializer = new NativeObjectSerializer();

        $serializedEnvelope = $serializedEnvelope = $serializer->serialize($envelope);
        $this->assertInternalType('string', $serializedEnvelope);
    }

    /**
     * @test
     */
    public function it_can_serialize_and_deserialize_a_default_message_envelope_with_a_serialized_message()
    {
        $originalEnvelope = DefaultEnvelope::forSerializedMessage(
            'SimpleBus\Serialization\Tests\Fixtures\DummyMessage',
            'serialized message'
        );
        $serializer = new NativeObjectSerializer();

        $serializedEnvelope = $serializedEnvelope = $serializer->serialize($originalEnvelope);
        $deserializedEnvelope = $serializer->deserialize($serializedEnvelope, get_class($originalEnvelope));
        $this->assertEquals($originalEnvelope, $deserializedEnvelope);
    }

    /**
     * @test
     */
    public function it_fails_when_the_deserialized_object_is_of_the_wrong_type()
    {
        $expectedType = 'SimpleBus\Serialization\Tests\Fixtures\DummyMessage';
        $message = new AnotherDummyMessage();
        $serializer = new NativeObjectSerializer();
        $serializedMessage = $serializer->serialize($message);

        $this->expectException('\LogicException', $expectedType);
        $serializer->deserialize($serializedMessage, $expectedType);
    }
}
