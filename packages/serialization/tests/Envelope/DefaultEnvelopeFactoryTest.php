<?php

namespace SimpleBus\Serialization\Tests\Message\Envelope;

use PHPUnit\Framework\TestCase;
use SimpleBus\Serialization\Envelope\DefaultEnvelopeFactory;
use SimpleBus\Serialization\Tests\Fixtures\DummyMessage;

class DefaultEnvelopeFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_a_default_message_envelope()
    {
        $factory = new DefaultEnvelopeFactory();

        $message = new DummyMessage();
        $envelope = $factory->wrapMessageInEnvelope($message);
        $this->assertInstanceOf('SimpleBus\Serialization\Envelope\DefaultEnvelope', $envelope);
        $this->assertSame(get_class($message), $envelope->messageType());
        $this->assertSame($message, $envelope->message());
    }

    /**
     * @test
     */
    public function it_returns_the_class_of_the_default_message_envelope()
    {
        $factory = new DefaultEnvelopeFactory();

        $defaultEnvelopeClass = 'SimpleBus\Serialization\Envelope\DefaultEnvelope';
        $this->assertSame($defaultEnvelopeClass, $factory->envelopeClass());
    }
}
