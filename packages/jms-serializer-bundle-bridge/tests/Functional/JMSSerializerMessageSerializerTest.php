<?php

namespace SimpleBus\JMSSerializerBundleBridge\Tests\Functional;

use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class JMSSerializerMessageSerializerTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
    }

    /**
     * @test
     */
    public function itSerializesAndDeserializesMessagesInEnvelopes(): void
    {
        $kernel = $this->createKernel([
            'debug' => false,
        ]);
        $kernel->boot();

        /** @var MessageInEnvelopeSerializer $messageSerializer */
        $messageSerializer = $kernel->getContainer()->get('public_message_serializer');
        $originalMessage = new SampleMessage('test', 123);

        $serializedMessageEnvelope = $messageSerializer->wrapAndSerialize($originalMessage);
        $deserializedEnvelope = $messageSerializer->unwrapAndDeserialize($serializedMessageEnvelope);
        $this->assertEquals($deserializedEnvelope->message(), $originalMessage);
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
