<?php

namespace SimpleBus\Asynchronous\Consumer;

use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer;
use SimpleBus\Message\Bus\MessageBus;

/**
 * Use this consumer to easily implement an asynchronous message consumer
 */
class StandardSerializedEnvelopeConsumer implements SerializedEnvelopeConsumer
{
    /**
     * @var MessageInEnvelopSerializer
     */
    private $messageInEnvelopeSerializer;

    /**
     * @var MessageBus
     */
    private $messageBus;

    public function __construct(MessageInEnvelopSerializer $messageInEnvelopeSerializer, MessageBus $messageBus)
    {
        $this->messageInEnvelopeSerializer = $messageInEnvelopeSerializer;
        $this->messageBus = $messageBus;
    }

    public function consume($serializedEnvelope)
    {
        $envelope = $this->messageInEnvelopeSerializer->unwrapAndDeserialize($serializedEnvelope);

        $this->messageBus->handle($envelope->message());
    }
}
