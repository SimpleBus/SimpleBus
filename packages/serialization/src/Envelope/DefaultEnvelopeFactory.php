<?php

namespace SimpleBus\Serialization\Envelope;

class DefaultEnvelopeFactory implements EnvelopeFactory
{
    public function wrapMessageInEnvelope(object $message): Envelope
    {
        return DefaultEnvelope::forMessage($message);
    }

    /**
     * @return class-string
     */
    public function envelopeClass(): string
    {
        return DefaultEnvelope::class;
    }
}
