<?php

namespace SimpleBus\Asynchronous\Tests\Message\Envelope\Serializer;

use SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelope;
use SimpleBus\Asynchronous\Message\Envelope\Envelope;
use SimpleBus\Asynchronous\Message\Envelope\Serializer\StandardMessageInEnvelopeSerializer;
use SimpleBus\Asynchronous\Tests\Message\Envelope\Serializer\Fixtures\DummyMessage;
use SimpleBus\Message\Message;

class StandardMessageInEnvelopeSerializerTest extends \PHPUnit_Framework_TestCase
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

        $envelopeClass = 'The\Envelope\Class';
        $serializedEnvelope = 'the serialized envelope';
        $envelope = DefaultEnvelope::forSerializedMessage($messageClass, $serializedMessage);
        $envelopeFactory = $this->envelopeFactoryForEnvelopeClass($envelopeClass);

        $objectSerializer = $this->mockObjectSerializerDeserializes([
            [$serializedEnvelope, $envelopeClass, $envelope],
            [$serializedMessage, $messageClass, $message]
        ]);

        $messageSerializer = new StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);
        $actualEnvelop = $messageSerializer->unwrapAndDeserialize($serializedEnvelope);

        $expectedEnvelop = $envelope->withMessage($message);
        $this->assertEquals($expectedEnvelop, $actualEnvelop);
    }

    private function envelopeFactoryCreatesEnvelope(Message $message, Envelope $expectedEnvelope)
    {
        $envelopeFactory = $this->getMock('SimpleBus\Asynchronous\Message\Envelope\EnvelopeFactory');
        $envelopeFactory
            ->expects($this->once())
            ->method('wrapMessageInEnvelope')
            ->with($this->equalTo($message))
            ->will($this->returnValue($expectedEnvelope));

        return $envelopeFactory;
    }

    private function mockObjectSerializer()
    {
        return $this->getMock('SimpleBus\Asynchronous\ObjectSerializer');
    }

    private function envelopeStub($type, $message)
    {
        return DefaultEnvelope::forSerializedMessage($type, $message);
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

    private function envelopeFactoryForEnvelopeClass($envelopeClass)
    {
        $envelopeFactory = $this->getMock('SimpleBus\Asynchronous\Message\Envelope\EnvelopeFactory');
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
