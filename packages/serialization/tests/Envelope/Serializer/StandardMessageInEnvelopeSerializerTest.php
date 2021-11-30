<?php

namespace SimpleBus\Serialization\Tests\Envelope\Serializer;

use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\Envelope\Envelope;
use SimpleBus\Serialization\Envelope\EnvelopeFactory;
use SimpleBus\Serialization\Envelope\Serializer\StandardMessageInEnvelopeSerializer;
use SimpleBus\Serialization\ObjectSerializer;
use SimpleBus\Serialization\Tests\Fixtures\DummyMessage;
use stdClass;

final class StandardMessageInEnvelopeSerializerTest extends TestCase
{
    /**
     * @test
     */
    public function itSerializesAMessageAndWrapsItInASerializedEnvelope(): void
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
    public function itDeserializesAMessageAfterUnwrappingItFromItsSerializedEnvelope(): void
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
    public function itFailsIfTheDeserializedEnvelopeIsNotOfTheExpectedType(): void
    {
        $envelopeClass = 'The\Envelope\Class';
        $serializedEnvelope = 'the serialized envelope';
        $notAnEnvelope = new stdClass();
        $envelopeFactory = $this->envelopeFactoryForEnvelopeClass($envelopeClass);

        $objectSerializer = $this->mockObjectSerializer();
        $objectSerializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($serializedEnvelope, $envelopeClass)
            ->willReturn($this->returnValue($notAnEnvelope));

        $messageSerializer = new StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);

        $this->expectException(LogicException::class);
        $messageSerializer->unwrapAndDeserialize($serializedEnvelope);
    }

    /**
     * @test
     */
    public function itFailsIfTheDeserializedMessageIsNotOfTheExpectedType(): void
    {
        $message = new DummyMessage();

        $messageClass = get_class($message);
        $serializedMessage = 'the serialized message';

        $envelope = DefaultEnvelope::forSerializedMessage($messageClass, $serializedMessage);
        $envelopeClass = get_class($envelope);
        $envelopeFactory = $this->envelopeFactoryForEnvelopeClass($envelopeClass);
        $serializedEnvelope = 'the serialized envelope';

        $notAMessage = new stdClass();

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

        $this->expectException(LogicException::class);
        $messageSerializer->unwrapAndDeserialize($serializedEnvelope);
    }

    /**
     * @param object $message
     *
     * @return EnvelopeFactory|MockObject
     */
    private function envelopeFactoryCreatesEnvelope($message, Envelope $expectedEnvelope)
    {
        $envelopeFactory = $this->createMock(EnvelopeFactory::class);
        $envelopeFactory
            ->expects($this->once())
            ->method('wrapMessageInEnvelope')
            ->with($this->equalTo($message))
            ->will($this->returnValue($expectedEnvelope));

        return $envelopeFactory;
    }

    /**
     * @return MockObject|ObjectSerializer
     */
    private function mockObjectSerializer()
    {
        return $this->createMock(ObjectSerializer::class);
    }

    /**
     * @return EnvelopeFactory|MockObject
     */
    private function envelopeFactoryForEnvelopeClass(string $envelopeClass)
    {
        $envelopeFactory = $this->createMock(EnvelopeFactory::class);
        $envelopeFactory
            ->expects($this->any())
            ->method('envelopeClass')
            ->will($this->returnValue($envelopeClass));

        return $envelopeFactory;
    }
}
