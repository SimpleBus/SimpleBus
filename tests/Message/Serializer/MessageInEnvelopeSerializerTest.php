<?php

namespace Message\Serializer;

use SimpleBus\Asynchronous\Message\Envelope\Envelope;
use SimpleBus\Asynchronous\Message\Serializer\MessageInEnvelopeSerializer;
use SimpleBus\Asynchronous\Tests\Message\Serializer\Fixtures\DummyMessage;

class MessageInEnvelopeSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_serializes_a_message_and_wraps_it_in_a_serialized_envelope()
    {
        $message = new DummyMessage();
        $messageType = get_class($message);
        $serializedMessage = 'the serialized message';

        $envelope = $this->dummyEnvelope();
        $serializedEnvelope = 'the serialized envelope';

        $envelopeFactory = $this->envelopeFactoryCreatesEnvelope($messageType, $serializedMessage, $envelope);

        $objectSerializer = $this->objectSerializerSerializes([
            [$message, $serializedMessage],
            [$envelope, $serializedEnvelope]
        ]);

        $messageSerializer = new MessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);
        $actualSerializedEnvelope = $messageSerializer->serialize($message);

        $this->assertSame($serializedEnvelope, $actualSerializedEnvelope);
    }

    /**
     * @test
     */
    public function it_deserializes_a_message_after_unwrapping_it_from_its_serialized_envelope()
    {
        $message = new DummyMessage();

        $messageClass = 'The\Message\Class';
        $serializedMessage = 'the serialized message';

        $envelopeClass = 'The\Envelope\Class';
        $serializedEnvelope = 'the serialized envelope';
        $envelope = $this->envelopeStub($messageClass, $serializedMessage);
        $envelopeFactory = $this->envelopeFactoryForEnvelopeClass($envelopeClass);

        $objectSerializer = $this->mockObjectSerializerDeserializes([
            [$serializedEnvelope, $envelopeClass, $envelope],
            [$serializedMessage, $messageClass, $message]
        ]);

        $messageSerializer = new MessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);
        $actualMessage = $messageSerializer->deserialize($serializedEnvelope);

        $this->assertSame($message, $actualMessage);
    }

    private function envelopeFactoryCreatesEnvelope($type, $message, Envelope $expectedEnvelope)
    {
        $envelopeFactory = $this->getMock('SimpleBus\Asynchronous\Message\Envelope\EnvelopeFactory');
        $envelopeFactory
            ->expects($this->once())
            ->method('createEnvelopeForSerializedMessage')
            ->with($type, $message)
            ->will($this->returnValue($expectedEnvelope));

        return $envelopeFactory;
    }

    private function dummyEnvelope()
    {
        return $this->getMock('SimpleBus\Asynchronous\Message\Envelope\Envelope');
    }

    private function mockObjectSerializer()
    {
        return $this->getMock('SimpleBus\Asynchronous\Message\Serializer\ObjectSerializer');
    }

    private function envelopeStub($type, $message)
    {
        $envelope = $this->getMock('SimpleBus\Asynchronous\Message\Envelope\Envelope');
        $envelope
            ->expects($this->any())
            ->method('type')
            ->will($this->returnValue($type));
        $envelope
            ->expects($this->any())
            ->method('message')
            ->will($this->returnValue($message));

        return $envelope;
    }

    private function objectSerializerSerializes(array $serializes)
    {
        $objectSerializer = $this->mockObjectSerializer();

        foreach ($serializes as $at => $objectAndSerializedObject) {
            list($object, $serializedObject) = $objectAndSerializedObject;
            $objectSerializer
                ->expects($this->at($at))
                ->method('serialize')
                ->with($this->identicalTo($object))
                ->will($this->returnValue($serializedObject));
        }

        return $objectSerializer;
    }

    private function envelopeFactoryForEnvelopeClass($envelopeClass)
    {
        $envelopeFactory = $this->getMock('SimpleBus\Asynchronous\Message\Envelope\EnvelopeFactory');
        $envelopeFactory
            ->expects($this->once())
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
