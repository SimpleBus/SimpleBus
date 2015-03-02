<?php

namespace Message\Envelope;

use SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelope;
use SimpleBus\Asynchronous\Tests\Message\Envelope\Serializer\Fixtures\DummyMessage;

class DefaultEnvelopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_an_envelope_for_a_message()
    {
        $message = new DummyMessage();
        $type = get_class($message);

        $envelope = DefaultEnvelope::forMessage($message);
        $this->assertInstanceOf('SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelope', $envelope);
        $this->assertSame($message, $envelope->message());
        $this->assertSame($type, $envelope->type());
    }

    /**
     * @test
     */
    public function it_creates_a_new_instance_for_a_different_message()
    {
        $message = new DummyMessage();
        $type = get_class($message);
        $envelope = DefaultEnvelope::forMessage($message);
        $aDifferentMessage = new DummyMessage();

        $newEnvelope = $envelope->withMessage($aDifferentMessage);

        $this->assertNotSame($envelope, $newEnvelope);
        $this->assertSame($aDifferentMessage, $newEnvelope->message());
        $this->assertSame($type, $newEnvelope->type());
    }

    /**
     * @test
     */
    public function it_creates_a_new_instance_for_a_serialized_version_of_the_message()
    {
        $message = new DummyMessage();
        $type = get_class($message);
        $envelope = DefaultEnvelope::forMessage($message);
        $serializedMessage = 'the serialized message';

        $newEnvelope = $envelope->withSerializedMessage($serializedMessage);

        $this->assertNotSame($envelope, $newEnvelope);
        $this->assertSame($message, $newEnvelope->message());
        $this->assertSame($serializedMessage, $newEnvelope->serializedMessage());
        $this->assertSame($type, $newEnvelope->type());
    }

    /**
     * @test
     */
    public function it_requires_the_type_to_be_a_subtype_of_message()
    {
        $this->setExpectedException('InvalidArgumentException', 'SimpleBus\Message\Message');
        DefaultEnvelope::forSerializedMessage('NotAMessageClass', 'any message');
    }

    /**
     * @test
     */
    public function it_fails_when_the_serialized_message_is_unavailable()
    {
        $message = new DummyMessage();
        $envelope = DefaultEnvelope::forMessage($message);

        $this->setExpectedException('LogicException');

        $envelope->serializedMessage();
    }

    /**
     * @test
     */
    public function it_fails_when_the_message_is_unavailable()
    {
        $envelope = DefaultEnvelope::forSerializedMessage(
            'SimpleBus\Asynchronous\Tests\Message\Envelope\Serializer\Fixtures\DummyMessage',
            'serialized message'
        );

        $this->setExpectedException('LogicException');

        $envelope->message();
    }
}
