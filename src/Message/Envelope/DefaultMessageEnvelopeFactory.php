<?php

namespace SimpleBus\Asynchronous\Message\Envelope;

class DefaultMessageEnvelopeFactory implements MessageEnvelopeFactory
{
    public function createEnvelopeForSerializedMessage($type, $serializedMessage)
    {
        return new DefaultMessageEnvelope($type, $serializedMessage);
    }

    public function envelopeClass()
    {
        return 'SimpleBus\Asynchronous\Message\Envelope\DefaultMessageEnvelope';
    }
}
