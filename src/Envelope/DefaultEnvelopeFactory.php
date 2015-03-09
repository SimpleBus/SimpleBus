<?php

namespace SimpleBus\Serialization\Envelope;

use SimpleBus\Message\Message;

class DefaultEnvelopeFactory implements EnvelopeFactory
{
    public function wrapMessageInEnvelope(Message $message)
    {
        return DefaultEnvelope::forMessage($message);
    }

    public function envelopeClass()
    {
        return 'SimpleBus\Serialization\Envelope\DefaultEnvelope';
    }
}
