<?php

namespace SimpleBus\JMSSerializerBundleBridge\Tests\Functional;

use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 * @coversNothing
 */
class JMSSerializerMessageSerializerTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
        static::$kernel = null;
    }

    /**
     * @test
     */
    public function itSerializesAndDeserializesMessagesInEnvelopes()
    {
        $kernel = $this->createKernel([
            'debug' => false,
        ]);
        $kernel->boot();
        $messageSerializer = $kernel->getContainer()->get('public_message_serializer');
        /** @var MessageInEnvelopeSerializer $messageSerializer */
        $originalMessage = new SampleMessage('test', 123);

        $serializedMessageEnvelope = $messageSerializer->wrapAndSerialize($originalMessage);
        $deserializedEnvelope = $messageSerializer->unwrapAndDeserialize($serializedMessageEnvelope);
        $this->assertEquals($deserializedEnvelope->message(), $originalMessage);
    }

    protected static function getKernelClass()
    {
        return 'SimpleBus\JMSSerializerBundleBridge\Tests\Functional\TestKernel';
    }
}
