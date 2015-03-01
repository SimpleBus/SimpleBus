<?php

namespace SimpleBus\JMSSerializerBridge;

use Assert\Assertion;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use SimpleBus\Asynchronous\Message\Envelope\MessageEnvelope;
use SimpleBus\Asynchronous\Message\Envelope\MessageEnvelopeFactory;
use SimpleBus\Asynchronous\Message\Serializer\MessageSerializer;
use SimpleBus\Message\Message;

class JMSSerializerMessageSerializer implements MessageSerializer
{
    /**
     * @var MessageEnvelopeFactory
     */
    private $messageEnvelopeFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $format;

    public function __construct(
        MessageEnvelopeFactory $messageEnvelopeFactory,
        SerializerInterface $serializer,
        $format
    ) {
        $this->messageEnvelopeFactory = $messageEnvelopeFactory;
        $this->serializer = $serializer;
        $this->format = $format;
    }

    public function serialize(Message $message)
    {
        $type = get_class($message);
        $messageBody = $this->serializeObject($message);
        $envelope = $this->messageEnvelopeFactory->createEnvelopeForSerializedMessage($type, $messageBody);

        return $this->serializeObject($envelope);
    }

    public function deserialize($serializedEnvelope)
    {
        $envelope = $this->deserializeObject($serializedEnvelope, $this->messageEnvelopeFactory->envelopeClass());
        /** @var $envelope MessageEnvelope */

        $message = $this->deserializeObject($envelope->serializedMessage(), $envelope->type());
        Assertion::isInstanceOf($message, 'SimpleBus\Message\Message');

        return $message;
    }

    private function serializeObject($object)
    {
        $serializationContext = SerializationContext::create()
            ->setSerializeNull(true);

        return $this->serializer->serialize($object, $this->format, $serializationContext);
    }

    private function deserializeObject($serializedObject, $type)
    {
        $deserializationContext = DeserializationContext::create()
            ->setSerializeNull(true);

        return $this->serializer->deserialize($serializedObject, $type, $this->format, $deserializationContext);
    }
}
