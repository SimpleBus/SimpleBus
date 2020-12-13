<?php

namespace SimpleBus\Serialization\Tests;

use LogicException;
use PHPUnit\Framework\TestCase;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\NativeObjectSerializer;
use SimpleBus\Serialization\Tests\Fixtures\AnotherDummyMessage;
use SimpleBus\Serialization\Tests\Fixtures\DummyMessage;

/**
 * @internal
 * @coversNothing
 */
class NativeObjectSerializerTest extends TestCase
{
    /**
     * @test
     */
    public function itCanSerializeADefaultMessageEnvelopeWithASerializedMessage(): void
    {
        $envelope = DefaultEnvelope::forSerializedMessage(
            DummyMessage::class,
            'serialized message'
        );
        $serializer = new NativeObjectSerializer();

        $serializedEnvelope = $serializedEnvelope = $serializer->serialize($envelope);
        $this->assertIsString($serializedEnvelope);
    }

    /**
     * @test
     */
    public function itCanSerializeAndDeserializeADefaultMessageEnvelopeWithASerializedMessage(): void
    {
        $originalEnvelope = DefaultEnvelope::forSerializedMessage(
            DummyMessage::class,
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
    public function itFailsWhenTheDeserializedObjectIsOfTheWrongType(): void
    {
        $expectedType = DummyMessage::class;
        $message = new AnotherDummyMessage();
        $serializer = new NativeObjectSerializer();
        $serializedMessage = $serializer->serialize($message);

        $this->expectException(LogicException::class);
        $serializer->deserialize($serializedMessage, $expectedType);
    }
}
