<?php

namespace SimpleBus\Asynchronous\Message\Envelope;

class DefaultEnvelopeFactory implements EnvelopeFactory
{
    public function createEnvelopeForSerializedMessage($type, $serializedMessage)
    {
        return new DefaultEnvelope($type, $serializedMessage);
    }

    public function envelopeClass()
    {
        return 'SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelope';
    }
}
