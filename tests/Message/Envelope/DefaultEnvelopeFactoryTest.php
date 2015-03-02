<?php

namespace SimpleBus\Asynchronous\Tests\Message\Envelope;

use SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelopeFactory;

class DefaultEnvelopeFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_default_message_envelope()
    {
        $factory = new DefaultEnvelopeFactory();
        $type = $this->dummyClassName();
        $serializedMessage = $this->dummySerializedMessage();

        $envelope = $factory->createEnvelopeForSerializedMessage($type, $serializedMessage);
        $this->assertInstanceOf('SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelope', $envelope);
        $this->assertSame($type, $envelope->type());
        $this->assertSame($serializedMessage, $envelope->message());
    }

    /**
     * @test
     */
    public function it_returns_the_class_of_the_default_message_envelope()
    {
        $factory = new DefaultEnvelopeFactory();

        $envelope = $factory->createEnvelopeForSerializedMessage(
            $this->dummyClassName(),
            $this->dummySerializedMessage()
        );

        $defaultEnvelopeClass = 'SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelope';
        $this->assertInstanceOf($defaultEnvelopeClass, $envelope);
        $this->assertSame($defaultEnvelopeClass, $factory->envelopeClass());
    }

    private function dummyClassName()
    {
        return 'SimpleBus\Asynchronous\Tests\Message\Envelope\Fixtures\DummyMessageClass';
    }

    private function dummySerializedMessage()
    {
        return '{"key":"value"}';
    }
}
