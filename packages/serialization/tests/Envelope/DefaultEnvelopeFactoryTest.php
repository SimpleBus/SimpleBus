<?php

namespace SimpleBus\Serialization\Tests\Message\Envelope;

use PHPUnit\Framework\TestCase;
use SimpleBus\Serialization\Envelope\DefaultEnvelopeFactory;
use SimpleBus\Serialization\Tests\Fixtures\DummyMessage;

/**
 * @internal
 * @coversNothing
 */
class DefaultEnvelopeFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itCreatesADefaultMessageEnvelope()
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
    public function itReturnsTheClassOfTheDefaultMessageEnvelope()
    {
        $factory = new DefaultEnvelopeFactory();

        $defaultEnvelopeClass = 'SimpleBus\Serialization\Envelope\DefaultEnvelope';
        $this->assertSame($defaultEnvelopeClass, $factory->envelopeClass());
    }
}
