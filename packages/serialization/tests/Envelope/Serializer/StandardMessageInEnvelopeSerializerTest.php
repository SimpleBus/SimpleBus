<?php

namespace SimpleBus\Serialization\Tests\Envelope\Serializer;

use PHPUnit\Framework\TestCase;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\Envelope\Envelope;
use SimpleBus\Serialization\Envelope\EnvelopeFactory;
use SimpleBus\Serialization\Envelope\Serializer\StandardMessageInEnvelopeSerializer;
use SimpleBus\Serialization\ObjectSerializer;
use SimpleBus\Serialization\Tests\Fixtures\DummyMessage;

/**
 * @internal
 * @coversNothing
 */
class StandardMessageInEnvelopeSerializerTest extends TestCase
{
    /**
     * @test
     */
    public function itSerializesAMessageAndWrapsItInASerializedEnvelope()
    {
        $message = new DummyMessage();
        $serializedMessage = 'the serialized message';

        $envelope = DefaultEnvelope::forMessage($message);
        $serializedEnvelope = 'the serialized envelope';

        $envelopeFactory = $this->envelopeFactoryCreatesEnvelope($message, $envelope);

        $objectSerializer = $this->mockObjectSerializer();

        $objectSerializer
            ->expects($this->exactly(2))
            ->method('serialize')
            ->withConsecutive(
                [$this->equalTo($message)],
                [$this->equalTo($envelope->withSerializedMessage($serializedMessage))],
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue($serializedMessage),
                $this->returnValue($serializedEnvelope),
            );

        $messageSerializer = new StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);
        $actualSerializedEnvelope = $messageSerializer->wrapAndSerialize($message);

        $this->assertEquals($serializedEnvelope, $actualSerializedEnvelope);
    }

    /**
     * @test
     */
    public function itDeserializesAMessageAfterUnwrappingItFromItsSerializedEnvelope()
    {
        $message = new DummyMessage();

        $messageClass = get_class($message);
        $serializedMessage = 'the serialized message';

        $envelope = DefaultEnvelope::forSerializedMessage($messageClass, $serializedMessage);
        $envelopeClass = get_class($envelope);
        $envelopeFactory = $this->envelopeFactoryForEnvelopeClass($envelopeClass);
        $serializedEnvelope = 'the serialized envelope';

        $objectSerializer = $this->mockObjectSerializer();
        $objectSerializer
            ->expects($this->exactly(2))
            ->method('deserialize')
            ->withConsecutive(
                [$serializedEnvelope, $envelopeClass],
                [$serializedMessage, $messageClass],
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue($envelope),
                $this->returnValue($message),
            );

        $messageSerializer = new StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);
        $actualEnvelop = $messageSerializer->unwrapAndDeserialize($serializedEnvelope);

        $expectedEnvelop = $envelope->withMessage($message);
        $this->assertEquals($expectedEnvelop, $actualEnvelop);
    }

    /**
     * @test
     */
    public function itFailsIfTheDeserializedEnvelopeIsNotOfTheExpectedType()
    {
        $envelopeClass = 'The\Envelope\Class';
        $serializedEnvelope = 'the serialized envelope';
        $notAnEnvelope = new \stdClass();
        $envelopeFactory = $this->envelopeFactoryForEnvelopeClass($envelopeClass);

        $objectSerializer = $this->mockObjectSerializer();
        $objectSerializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($serializedEnvelope, $envelopeClass)
            ->willReturn($this->returnValue($notAnEnvelope));

        $messageSerializer = new StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);

        $this->expectException('\LogicException');
        $messageSerializer->unwrapAndDeserialize($serializedEnvelope);
    }

    /**
     * @test
     */
    public function itFailsIfTheDeserializedMessageIsNotOfTheExpectedType()
    {
        $message = new DummyMessage();

        $messageClass = get_class($message);
        $serializedMessage = 'the serialized message';

        $envelope = DefaultEnvelope::forSerializedMessage($messageClass, $serializedMessage);
        $envelopeClass = get_class($envelope);
        $envelopeFactory = $this->envelopeFactoryForEnvelopeClass($envelopeClass);
        $serializedEnvelope = 'the serialized envelope';

        $notAMessage = new \stdClass();

        $objectSerializer = $this->mockObjectSerializer();
        $objectSerializer
            ->expects($this->exactly(2))
            ->method('deserialize')
            ->withConsecutive(
                [$serializedEnvelope, $envelopeClass],
                [$serializedMessage, $messageClass],
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue($envelope),
                $this->returnValue($notAMessage),
            );

        $messageSerializer = new StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);

        $this->expectException('\LogicException', $messageClass);
        $messageSerializer->unwrapAndDeserialize($serializedEnvelope);
    }

    /**
     * @param object $message
     *
     * @return EnvelopeFactory|\PHPUnit\Framework\MockObject\MockObject
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
     * @return ObjectSerializer|\PHPUnit\Framework\MockObject\MockObject
     */
    private function mockObjectSerializer()
    {
        return $this->createMock('SimpleBus\Serialization\ObjectSerializer');
    }

    /**
     * @param $envelopeClass
     *
     * @return EnvelopeFactory|\PHPUnit\Framework\MockObject\MockObject
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
}
