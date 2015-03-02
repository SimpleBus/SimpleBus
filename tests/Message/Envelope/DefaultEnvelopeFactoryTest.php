<?php

namespace SimpleBus\Asynchronous\Tests\Message\Envelope;

use SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelopeFactory;
use SimpleBus\Asynchronous\Tests\Message\Envelope\Serializer\Fixtures\DummyMessage;

class DefaultEnvelopeFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_default_message_envelope()
    {
        $factory = new DefaultEnvelopeFactory();

        $message = new DummyMessage();
        $envelope = $factory->wrapMessageInEnvelope($message);
        $this->assertInstanceOf('SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelope', $envelope);
        $this->assertSame(get_class($message), $envelope->type());
        $this->assertSame($message, $envelope->message());
    }

    /**
     * @test
     */
    public function it_returns_the_class_of_the_default_message_envelope()
    {
        $factory = new DefaultEnvelopeFactory();

        $defaultEnvelopeClass = 'SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelope';
        $this->assertSame($defaultEnvelopeClass, $factory->envelopeClass());
    }
}
