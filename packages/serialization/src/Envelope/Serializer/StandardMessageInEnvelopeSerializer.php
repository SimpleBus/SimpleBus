<?php

namespace SimpleBus\Serialization\Envelope\Serializer;

use LogicException;
use SimpleBus\Serialization\Envelope\Envelope;
use SimpleBus\Serialization\Envelope\EnvelopeFactory;
use SimpleBus\Serialization\ObjectSerializer;

class StandardMessageInEnvelopeSerializer implements MessageInEnvelopeSerializer
{
    private EnvelopeFactory $envelopeFactory;

    private ObjectSerializer $objectSerializer;

    public function __construct(
        EnvelopeFactory $envelopeFactory,
        ObjectSerializer $objectSerializer
    ) {
        $this->envelopeFactory = $envelopeFactory;
        $this->objectSerializer = $objectSerializer;
    }

    /**
     * Serialize a Message by wrapping it in an Envelope and serializing the envelope.
     */
    public function wrapAndSerialize(object $message): string
    {
        $envelope = $this->envelopeFactory->wrapMessageInEnvelope($message);

        $serializedMessage = $this->objectSerializer->serialize($message);

        return $this->objectSerializer->serialize($envelope->withSerializedMessage($serializedMessage));
    }

    /**
     * Deserialize a Message that was wrapped in an Envelope.
     */
    public function unwrapAndDeserialize(string $serializedEnvelope): Envelope
    {
        $envelope = $this->deserializeEnvelope($serializedEnvelope);

        $message = $this->deserializeMessage($envelope->serializedMessage(), $envelope->messageType());

        return $envelope->withMessage($message);
    }

    /**
     * Deserialize the message Envelope.
     */
    private function deserializeEnvelope(string $serializedEnvelope): Envelope
    {
        $envelopeClass = $this->envelopeFactory->envelopeClass();
        $envelope = $this->objectSerializer->deserialize(
            $serializedEnvelope,
            $envelopeClass
        );

        if (!$envelope instanceof $envelopeClass) {
            throw new LogicException(sprintf('Expected deserialized object to be an instance of "%s"', $envelopeClass));
        }

        if (!$envelope instanceof Envelope) {
            throw new LogicException(sprintf('Expected deserialized object to be an instance of "%s"', Envelope::class));
        }

        return $envelope;
    }

    /**
     * Deserialize the Message.
     *
     * @param class-string $messageClass
     */
    private function deserializeMessage(string $serializedMessage, string $messageClass): object
    {
        $message = $this->objectSerializer->deserialize($serializedMessage, $messageClass);

        if (!$message instanceof $messageClass) {
            throw new LogicException(sprintf('Expected deserialized message to be an instance of "%s"', $messageClass));
        }

        return $message;
    }
}
