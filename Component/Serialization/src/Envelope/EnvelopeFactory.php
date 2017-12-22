<?php

namespace SimpleBus\Serialization\Envelope;

interface EnvelopeFactory
{
    /**
     * Create an Envelope for a message
     *
     * @param object $message
     * @return Envelope
     */
    public function wrapMessageInEnvelope($message);

    /**
     * The FQCN of the Envelope instances created by this factory
     *
     * @return string
     */
    public function envelopeClass();
}
