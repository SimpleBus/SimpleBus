<?php

namespace SimpleBus\JMSSerializerBundle\Tests\Functional;

use SimpleBus\Asynchronous\Message\Envelope\Serializer\MessageInEnvelopSerializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class JMSSerializerMessageSerializerTest extends KernelTestCase
{
    protected static function getKernelClass()
    {
        return 'SimpleBus\JMSSerializerBundle\Tests\Functional\TestKernel';
    }

    /**
     * @test
     */
    public function it_serializes_and_deserializes_messages_in_envelopes()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $messageSerializer = $kernel->getContainer()->get('public_message_serializer');
        /** @var MessageInEnvelopSerializer $messageSerializer */

        $originalMessage = new SampleMessage('test', 123);

        $serializedMessageEnvelope = $messageSerializer->wrapAndSerialize($originalMessage);
        $deserializedEnvelope = $messageSerializer->unwrapAndDeserialize($serializedMessageEnvelope);
        $this->assertEquals($deserializedEnvelope->message(), $originalMessage);
    }
}
