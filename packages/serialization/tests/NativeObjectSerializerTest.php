<?php

namespace SimpleBus\Serialization\Tests;

use PHPUnit\Framework\TestCase;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\NativeObjectSerializer;
use SimpleBus\Serialization\Tests\Fixtures\AnotherDummyMessage;

class NativeObjectSerializerTest extends TestCase
{
    /**
     * @test
     */
    public function itCanSerializeADefaultMessageEnvelopeWithASerializedMessage()
    {
        $envelope = DefaultEnvelope::forSerializedMessage(
            'SimpleBus\Serialization\Tests\Fixtures\DummyMessage',
            'serialized message'
        );
        $serializer = new NativeObjectSerializer();

        $serializedEnvelope = $serializedEnvelope = $serializer->serialize($envelope);
        $this->assertIsString($serializedEnvelope);
    }

    /**
     * @test
     */
    public function itCanSerializeAndDeserializeADefaultMessageEnvelopeWithASerializedMessage()
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
    public function itFailsWhenTheDeserializedObjectIsOfTheWrongType()
    {
        $expectedType = 'SimpleBus\Serialization\Tests\Fixtures\DummyMessage';
        $message = new AnotherDummyMessage();
        $serializer = new NativeObjectSerializer();
        $serializedMessage = $serializer->serialize($message);

        $this->expectException('\LogicException', $expectedType);
        $serializer->deserialize($serializedMessage, $expectedType);
    }
}
