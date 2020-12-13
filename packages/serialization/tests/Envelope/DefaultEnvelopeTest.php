<?php

namespace Message\Envelope;

use PHPUnit\Framework\TestCase;
use SimpleBus\Serialization\Envelope\DefaultEnvelope;
use SimpleBus\Serialization\Tests\Fixtures\DummyMessage;

class DefaultEnvelopeTest extends TestCase
{
    /**
     * @test
     */
    public function itCreatesAnEnvelopeForAMessage()
    {
        $message = new DummyMessage();
        $type = get_class($message);

        $envelope = DefaultEnvelope::forMessage($message);
        $this->assertInstanceOf('SimpleBus\Serialization\Envelope\DefaultEnvelope', $envelope);
        $this->assertSame($message, $envelope->message());
        $this->assertSame($type, $envelope->messageType());
    }

    /**
     * @test
     */
    public function itCreatesANewInstanceForADifferentMessage()
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
    public function itCreatesANewInstanceForASerializedVersionOfTheMessage()
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
    public function itFailsWhenTheSerializedMessageIsUnavailable()
    {
        $message = new DummyMessage();
        $envelope = DefaultEnvelope::forMessage($message);

        $this->expectException('LogicException');

        $envelope->serializedMessage();
    }

    /**
     * @test
     */
    public function itFailsWhenTheMessageIsUnavailable()
    {
        $envelope = DefaultEnvelope::forSerializedMessage(
            'SimpleBus\Serialization\Tests\Fixtures\DummyMessage',
            'serialized message'
        );

        $this->expectException('LogicException');

        $envelope->message();
    }
}
