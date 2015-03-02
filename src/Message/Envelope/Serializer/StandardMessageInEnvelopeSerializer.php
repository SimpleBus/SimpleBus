<?php

namespace SimpleBus\Asynchronous\Message\Envelope\Serializer;

use SimpleBus\Asynchronous\Message\Envelope\Envelope;
use SimpleBus\Asynchronous\Message\Envelope\EnvelopeFactory;
use SimpleBus\Asynchronous\ObjectSerializer;
use SimpleBus\Message\Message;

class StandardMessageInEnvelopeSerializer implements MessageInEnvelopSerializer
{
    /**
     * @var EnvelopeFactory
     */
    private $envelopeFactory;

    /**
     * @var \SimpleBus\Asynchronous\ObjectSerializer
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
    public function wrapAndSerialize(Message $message)
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
        $envelope = $this->objectSerializer->deserialize(
            $serializedEnvelope,
            $this->envelopeFactory->envelopeClass()
        );
        /** @var $envelope Envelope */
        $message = $this->objectSerializer->deserialize($envelope->serializedMessage(), $envelope->type());

        return $envelope->withMessage($message);
    }
}
