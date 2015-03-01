<?php

namespace SimpleBus\JMSSerializerBridge\Tests\Integration;

use JMS\Serializer\SerializerBuilder;
use SimpleBus\Asynchronous\Message\Envelope\DefaultMessageEnvelopeFactory;
use SimpleBus\JMSSerializerBridge\JMSSerializerMessageSerializer;
use SimpleBus\JMSSerializerBridge\SerializerMetadata;

class JMSSerializerMessageSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_serializes_and_deserializes_messages()
    {
        $messageEnvelopeFactory = new DefaultMessageEnvelopeFactory();
        $format = 'json';
        $jmsSerializer = SerializerBuilder::create()
            ->addMetadataDir(SerializerMetadata::dir(), 'SimpleBus\Asynchronous')
            ->build();
        $originalMessage = new SampleMessage('test', 123);

        $messageSerializer = new JMSSerializerMessageSerializer($messageEnvelopeFactory, $jmsSerializer, $format);
        $serializedMessageEnvelope = $messageSerializer->serialize($originalMessage);
        $deserializedMessage = $messageSerializer->deserialize($serializedMessageEnvelope);
        $this->assertEquals($deserializedMessage, $originalMessage);
    }
}
