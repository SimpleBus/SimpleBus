<?php

namespace SimpleBus\Asynchronous\Consumer;

use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer;

/**
 * Use this consumer to easily implement an asynchronous message consumer.
 */
class StandardSerializedEnvelopeConsumer implements SerializedEnvelopeConsumer
{
    private MessageInEnvelopeSerializer $messageInEnvelopeSerializer;

    private MessageBus $messageBus;

    public function __construct(MessageInEnvelopeSerializer $messageInEnvelopeSerializer, MessageBus $messageBus)
    {
        $this->messageInEnvelopeSerializer = $messageInEnvelopeSerializer;
        $this->messageBus = $messageBus;
    }

    public function consume(string $serializedEnvelope): void
    {
        $envelope = $this->messageInEnvelopeSerializer->unwrapAndDeserialize($serializedEnvelope);

        $this->messageBus->handle($envelope->message());
    }
}
