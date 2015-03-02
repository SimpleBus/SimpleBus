<?php

namespace SimpleBus\Asynchronous\Message\Envelope;

interface EnvelopeFactory
{
    /**
     * Create an Envelope or a Message of the given type and serialized message
     *
     * @param string $type
     * @param string $serializedMessage
     * @return Envelope
     */
    public function createEnvelopeForSerializedMessage($type, $serializedMessage);

    /**
     * The FQCN of the Envelope instances created by this factory
     *
     * @return string
     */
    public function envelopeClass();
}
