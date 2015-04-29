<?php

namespace SimpleBus\Serialization\Envelope\Serializer;

use SimpleBus\Serialization\Envelope\Envelope;
use SimpleBus\Serialization\Envelope\EnvelopeFactory;
use SimpleBus\Serialization\ObjectSerializer;

class StandardMessageInEnvelopeSerializer implements MessageInEnvelopSerializer
{
    /**
     * @var EnvelopeFactory
     */
    private $envelopeFactory;

    /**
     * @var \SimpleBus\Serialization\ObjectSerializer
     */
    private $objectSerializer;

    public function __construct(
        EnvelopeFactory $envelopeFactory,
        ObjectSerializer $objectSerializer
    ) {
        $this->envelopeFactory = $envelopeFactory;
        $this->objectSerializer = $objectSerializer;
    }

    /**
     * Serialize a Message by wrapping it in an Envelope and serializing the envelope
     *
     * @{inheritdoc}
     */
    public function wrapAndSerialize($message)
    {
        $envelope = $this->envelopeFactory->wrapMessageInEnvelope($message);

        $serializedMessage = $this->objectSerializer->serialize($message);

        return $this->objectSerializer->serialize($envelope->withSerializedMessage($serializedMessage));
    }

    /**
     * Deserialize a Message that was wrapped in an Envelope
     *
     * @{inheritdoc}
     */
    public function unwrapAndDeserialize($serializedEnvelope)
    {
        $envelope = $this->deserializeEnvelope($serializedEnvelope);

        $message = $this->deserializeMessage($envelope->serializedMessage(), $envelope->messageType());

        return $envelope->withMessage($message);
    }

    /**
     * Deserialize the message Envelope
     *
     * @param string $serializedEnvelope
     * @return Envelope
     */
    private function deserializeEnvelope($serializedEnvelope)
    {
        $envelopeClass = $this->envelopeFactory->envelopeClass();
        $envelope = $this->objectSerializer->deserialize(
            $serializedEnvelope,
            $envelopeClass
        );

        if (!($envelope instanceof $envelopeClass)) {
            throw new \LogicException(
                sprintf(
                    'Expected deserialized object to be an instance of "%s"',
                    $envelopeClass
                )
            );
        }

        return $envelope;
    }

    /**
     * Deserialize the Message
     *
     * @param string $serializedMessage
     * @param string $messageClass
     * @return object Of type $messageClass
     */
    private function deserializeMessage($serializedMessage, $messageClass)
    {
        $message = $this->objectSerializer->deserialize($serializedMessage, $messageClass);

        if (!($message instanceof $messageClass)) {
            throw new \LogicException(
                sprintf(
                    'Expected deserialized message to be an instance of "%s"',
                    $messageClass
                )
            );
        }

        return $message;
    }
}
