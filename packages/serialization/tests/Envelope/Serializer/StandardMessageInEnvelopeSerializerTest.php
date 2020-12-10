<?php

namespace SimpleBus\Serialization\Tests\Envelope\Serializer;

use PHPUnit\Framework\TestCase;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\Envelope\Envelope;
use SimpleBus\Serialization\Envelope\EnvelopeFactory;
use SimpleBus\Serialization\Envelope\Serializer\StandardMessageInEnvelopeSerializer;
use SimpleBus\Serialization\ObjectSerializer;
use SimpleBus\Serialization\Tests\Fixtures\DummyMessage;

class StandardMessageInEnvelopeSerializerTest extends TestCase
{
    /**
     * @test
     */
    public function it_serializes_a_message_and_wraps_it_in_a_serialized_envelope()
    {
        $message = new DummyMessage();
        $serializedMessage = 'the serialized message';

        $envelope = DefaultEnvelope::forMessage($message);
        $serializedEnvelope = 'the serialized envelope';

        $envelopeFactory = $this->envelopeFactoryCreatesEnvelope($message, $envelope);

        $objectSerializer = $this->objectSerializerSerializes([
            [$message, $serializedMessage],
            [$envelope->withSerializedMessage($serializedMessage), $serializedEnvelope]
        ]);

        $messageSerializer = new StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);
        $actualSerializedEnvelope = $messageSerializer->wrapAndSerialize($message);

        $this->assertEquals($serializedEnvelope, $actualSerializedEnvelope);
    }

    /**
     * @test
     */
    public function it_deserializes_a_message_after_unwrapping_it_from_its_serialized_envelope()
    {
        $message = new DummyMessage();

        $messageClass = get_class($message);
        $serializedMessage = 'the serialized message';

        $envelope = DefaultEnvelope::forSerializedMessage($messageClass, $serializedMessage);
        $envelopeClass = get_class($envelope);
        $envelopeFactory = $this->envelopeFactoryForEnvelopeClass($envelopeClass);
        $serializedEnvelope = 'the serialized envelope';

        $objectSerializer = $this->mockObjectSerializerDeserializes([
            [$serializedEnvelope, $envelopeClass, $envelope],
            [$serializedMessage, $messageClass, $message]
        ]);

        $messageSerializer = new StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);
        $actualEnvelop = $messageSerializer->unwrapAndDeserialize($serializedEnvelope);

        $expectedEnvelop = $envelope->withMessage($message);
        $this->assertEquals($expectedEnvelop, $actualEnvelop);
    }

    /**
     * @test
     */
    public function it_fails_if_the_deserialized_envelope_is_not_of_the_expected_type()
    {
        $envelopeClass = 'The\Envelope\Class';
        $serializedEnvelope = 'the serialized envelope';
        $notAnEnvelope = new \stdClass();
        $envelopeFactory = $this->envelopeFactoryForEnvelopeClass($envelopeClass);

        $objectSerializer = $this->mockObjectSerializerDeserializes([
            [$serializedEnvelope, $envelopeClass, $notAnEnvelope],
        ]);

        $messageSerializer = new StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);

        $this->expectException('\LogicException');
        $messageSerializer->unwrapAndDeserialize($serializedEnvelope);
    }

    /**
     * @test
     */
    public function it_fails_if_the_deserialized_message_is_not_of_the_expected_type()
    {
        $message = new DummyMessage();

        $messageClass = get_class($message);
        $serializedMessage = 'the serialized message';

        $envelope = DefaultEnvelope::forSerializedMessage($messageClass, $serializedMessage);
        $envelopeClass = get_class($envelope);
        $envelopeFactory = $this->envelopeFactoryForEnvelopeClass($envelopeClass);
        $serializedEnvelope = 'the serialized envelope';

        $notAMessage = new \stdClass();
        $objectSerializer = $this->mockObjectSerializerDeserializes([
            [$serializedEnvelope, $envelopeClass, $envelope],
            [$serializedMessage, $messageClass, $notAMessage]
        ]);

        $messageSerializer = new StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);

        $this->expectException('\LogicException', $messageClass);
        $messageSerializer->unwrapAndDeserialize($serializedEnvelope);
    }

    /**
     * @param object $message
     * @param Envelope $expectedEnvelope
     * @return \PHPUnit\Framework\MockObject\MockObject|EnvelopeFactory
     */
    private function envelopeFactoryCreatesEnvelope($message, Envelope $expectedEnvelope)
    {
        $envelopeFactory = $this->createMock('SimpleBus\Serialization\Envelope\EnvelopeFactory');
        $envelopeFactory
            ->expects($this->once())
            ->method('wrapMessageInEnvelope')
            ->with($this->equalTo($message))
            ->will($this->returnValue($expectedEnvelope));

        return $envelopeFactory;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|ObjectSerializer
     */
    private function mockObjectSerializer()
    {
        return $this->createMock('SimpleBus\Serialization\ObjectSerializer');
    }

    private function objectSerializerSerializes(array $serializes)
    {
        $objectSerializer = $this->mockObjectSerializer();

        foreach ($serializes as $at => $objectAndSerializedObject) {
            list($object, $serializedObject) = $objectAndSerializedObject;
            $objectSerializer
                ->expects($this->at($at))
                ->method('serialize')
                ->with($this->equalTo($object))
                ->will($this->returnValue($serializedObject));
        }

        return $objectSerializer;
    }

    /**
     * @param $envelopeClass
     * @return \PHPUnit\Framework\MockObject\MockObject|EnvelopeFactory
     */
    private function envelopeFactoryForEnvelopeClass($envelopeClass)
    {
        $envelopeFactory = $this->createMock('SimpleBus\Serialization\Envelope\EnvelopeFactory');
        $envelopeFactory
            ->expects($this->any())
            ->method('envelopeClass')
            ->will($this->returnValue($envelopeClass));

        return $envelopeFactory;
    }

    private function mockObjectSerializerDeserializes(array $deserializes)
    {
        $objectSerializer = $this->mockObjectSerializer();

        foreach ($deserializes as $at => $data) {
            list($serialized, $type, $expectedObject) = $data;

            $objectSerializer
                ->expects($this->at($at))
                ->method('deserialize')
                ->with($serialized, $type)
                ->will($this->returnValue($expectedObject));
        }

        return $objectSerializer;
    }
}
