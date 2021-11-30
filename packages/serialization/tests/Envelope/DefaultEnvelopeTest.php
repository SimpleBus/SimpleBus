<?php

namespace Message\Envelope;

use LogicException;
use PHPUnit\Framework\TestCase;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\Tests\Fixtures\DummyMessage;

class DefaultEnvelopeTest extends TestCase
{
    /**
     * @test
     */
    public function itCreatesAnEnvelopeForAMessage(): void
    {
        $message = new DummyMessage();
        $type = get_class($message);

        $envelope = DefaultEnvelope::forMessage($message);
        $this->assertInstanceOf(DefaultEnvelope::class, $envelope);
        $this->assertSame($message, $envelope->message());
        $this->assertSame($type, $envelope->messageType());
    }

    /**
     * @test
     */
    public function itCreatesANewInstanceForADifferentMessage(): void
    {
        $message = new DummyMessage();
        $type = get_class($message);
        $envelope = DefaultEnvelope::forMessage($message);
        $aDifferentMessage = new DummyMessage();

        $newEnvelope = $envelope->withMessage($aDifferentMessage);

        $this->assertNotSame($envelope, $newEnvelope);
        $this->assertSame($aDifferentMessage, $newEnvelope->message());
        $this->assertSame($type, $newEnvelope->messageType());
    }

    /**
     * @test
     */
    public function itCreatesANewInstanceForASerializedVersionOfTheMessage(): void
    {
        $message = new DummyMessage();
        $type = get_class($message);
        $envelope = DefaultEnvelope::forMessage($message);
        $serializedMessage = 'the serialized message';

        $newEnvelope = $envelope->withSerializedMessage($serializedMessage);

        $this->assertNotSame($envelope, $newEnvelope);
        $this->assertSame($message, $newEnvelope->message());
        $this->assertSame($serializedMessage, $newEnvelope->serializedMessage());
        $this->assertSame($type, $newEnvelope->messageType());
    }

    /**
     * @test
     */
    public function itFailsWhenTheSerializedMessageIsUnavailable(): void
    {
        $message = new DummyMessage();
        $envelope = DefaultEnvelope::forMessage($message);

        $this->expectException(LogicException::class);

        $envelope->serializedMessage();
    }

    /**
     * @test
     */
    public function itFailsWhenTheMessageIsUnavailable(): void
    {
        $envelope = DefaultEnvelope::forSerializedMessage(
            DummyMessage::class,
            'serialized message'
        );

        $this->expectException(LogicException::class);

        $envelope->message();
    }
}
