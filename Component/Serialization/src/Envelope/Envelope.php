<?php

namespace SimpleBus\Serialization\Envelope;

interface Envelope
{
    /**
     * The type (FQCN) of the message
     *
     * @return string
     */
    public function messageType();

    /**
     * The message
     *
     * @throws \LogicException When the Message has not been provided
     * @return object
     */
    public function message();

    /**
     * A new instance of the same class, with the same type, but another message
     *
     * @param object $message
     * @return Envelope
     */
    public function withMessage($message);

    /**
     * A new instance of the same class, with a serialized message of the same type
     *
     * @param string $serializedMessage
     * @return Envelope
     */
    public function withSerializedMessage($serializedMessage);

    /**
     * @throws \LogicException When the serialized message has not been provided
     * @return string
     */
    public function serializedMessage();
}
