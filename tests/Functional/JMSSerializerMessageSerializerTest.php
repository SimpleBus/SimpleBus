<?php

namespace SimpleBus\JMSSerializerBundle\Tests\Functional;

use SimpleBus\JMSSerializerBridge\JMSSerializerMessageSerializer;
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
    public function it_serializes_and_deserializes_messages()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $messageSerializer = $kernel->getContainer()->get('public_message_serializer');
        /** @var JMSSerializerMessageSerializer $messageSerializer */

        $originalMessage = new SampleMessage('test', 123);

        $serializedMessageEnvelope = $messageSerializer->serialize($originalMessage);
        $deserializedMessage = $messageSerializer->deserialize($serializedMessageEnvelope);
        $this->assertEquals($deserializedMessage, $originalMessage);
    }
}
