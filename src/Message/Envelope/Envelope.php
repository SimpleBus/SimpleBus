<?php

namespace SimpleBus\Asynchronous\Message\Envelope;

use SimpleBus\Message\Message;

interface Envelope
{
    /**
     * The type (FQCN) of the message
     *
     * @return string
     */
    public function type();

    /**
     * The message
     *
     * @throws \LogicException When the Message has not been provided
     * @return mixed
     */
    public function message();

    /**
     * A new instance of the same class, with the same type, but another message
     *
     * @param Message $message
     * @return Envelope
     */
    public function withMessage(Message $message);

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
