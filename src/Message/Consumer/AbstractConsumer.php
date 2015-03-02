<?php

namespace SimpleBus\Asynchronous\Message\Consumer;

use SimpleBus\Asynchronous\Message\Envelope\Serializer\MessageInEnvelopSerializer;
use SimpleBus\Message\Bus\MessageBus;

/**
 * Extend from this consumer to easily implement an asynchronous message consumer
 */
abstract class AbstractConsumer
{
    /**
     * @var EnvelopeSerializer
     */
    private $envelopeSerializer;

    /**
     * @var MessageBus
     */
    private $messageBus;

    public function __construct(MessageInEnvelopSerializer $envelopeSerializer, MessageBus $messageBus)
    {
        $this->envelopeSerializer = $envelopeSerializer;
        $this->messageBus = $messageBus;
    }

    /**
     * Consume a serialized Envelope, which includes a serialized Message
     *
     * @param string $serializedEnvelope
     */
    protected function consume($serializedEnvelope)
    {
        $envelope = $this->envelopeSerializer->unwrapAndDeserialize($serializedEnvelope);

        $this->messageBus->handle($envelope->message());
    }
}
