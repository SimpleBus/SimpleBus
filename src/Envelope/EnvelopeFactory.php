<?php

namespace SimpleBus\Serialization\Envelope;

use SimpleBus\Message\Message;

interface EnvelopeFactory
{
    /**
     * Create an Envelope for a message
     *
     * @param Message $message
     * @return Envelope
     */
    public function wrapMessageInEnvelope(Message $message);

    /**
     * The FQCN of the Envelope instances created by this factory
     *
     * @return string
     */
    public function envelopeClass();
}
