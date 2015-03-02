<?php

namespace SimpleBus\Asynchronous\Message\Envelope;

interface Envelope
{
    /**
     * The type (FQCN) of the serialized message
     *
     * @return string
     */
    public function type();

    /**
     * The serialized message
     *
     * @return string
     */
    public function message();
}
