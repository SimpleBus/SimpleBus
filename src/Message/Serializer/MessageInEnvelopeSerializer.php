<?php

namespace SimpleBus\Asynchronous\Message\Serializer;

use Assert\Assertion;
use SimpleBus\Asynchronous\Message\Envelope\Envelope;
use SimpleBus\Asynchronous\Message\Envelope\EnvelopeFactory;
use SimpleBus\Message\Message;

class MessageInEnvelopeSerializer implements MessageSerializer
{
    /**
     * @var EnvelopeFactory
     */
    private $envelopeFactory;

    /**
     * @var ObjectSerializer
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
     * Serialize a Message by wrapping it in an Envelope
     *
     * @{inheritdoc}
     */
    public function serialize(Message $message)
    {
        $type = get_class($message);
        $messageBody = $this->objectSerializer->serialize($message);
        $envelope = $this->envelopeFactory->createEnvelopeForSerializedMessage($type, $messageBody);

        return $this->objectSerializer->serialize($envelope);
    }

    /**
     * Deserialize a Message that was wrapped in an Envelope
     *
     * @{inheritdoc}
     */
    public function deserialize($serializedMessage)
    {
        $envelope = $this->objectSerializer->deserialize(
            $serializedMessage,
            $this->envelopeFactory->envelopeClass()
        );
        /** @var $envelope Envelope */

        $message = $this->objectSerializer->deserialize($envelope->message(), $envelope->type());
        Assertion::isInstanceOf($message, 'SimpleBus\Message\Message');

        return $message;
    }
}
