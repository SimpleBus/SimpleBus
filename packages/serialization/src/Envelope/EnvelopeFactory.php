<?php

namespace SimpleBus\Serialization\Envelope;

interface EnvelopeFactory
{
    /**
     * Create an Envelope for a message.
     */
    public function wrapMessageInEnvelope(object $message): Envelope;

    /**
     * The FQCN of the Envelope instances created by this factory.
     *
     * @return class-string
     */
    public function envelopeClass(): string;
}
