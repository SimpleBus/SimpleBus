<?php

namespace SimpleBus\Asynchronous\Tests\Message\Envelope;

use SimpleBus\Asynchronous\Message\Envelope\DefaultMessageEnvelopeFactory;

class DefaultMessageEnvelopeFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_default_message_envelope()
    {
        $factory = new DefaultMessageEnvelopeFactory();
        $type = $this->dummyClassName();
        $serializedMessage = $this->dummySerializedMessage();

        $envelope = $factory->createEnvelopeForSerializedMessage($type, $serializedMessage);
        $this->assertInstanceOf('SimpleBus\Asynchronous\Message\Envelope\DefaultMessageEnvelope', $envelope);
        $this->assertSame($type, $envelope->type());
        $this->assertSame($serializedMessage, $envelope->serializedMessage());
    }

    /**
     * @test
     */
    public function it_returns_the_class_of_the_default_message_envelope()
    {
        $factory = new DefaultMessageEnvelopeFactory();

        $envelope = $factory->createEnvelopeForSerializedMessage(
            $this->dummyClassName(),
            $this->dummySerializedMessage()
        );

        $defaultMessageEnvelopeClass = 'SimpleBus\Asynchronous\Message\Envelope\DefaultMessageEnvelope';
        $this->assertInstanceOf($defaultMessageEnvelopeClass, $envelope);
        $this->assertSame($defaultMessageEnvelopeClass, $factory->envelopeClass());
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
