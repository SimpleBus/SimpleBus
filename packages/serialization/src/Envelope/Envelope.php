<?php

namespace SimpleBus\Serialization\Envelope;

use LogicException;

interface Envelope
{
    /**
     * The type (FQCN) of the message.
     *
     * @return class-string
     */
    public function messageType(): string;

    /**
     * The message.
     *
     * @throws LogicException When the Message has not been provided
     */
    public function message(): object;

    /**
     * A new instance of the same class, with the same type, but another message.
     */
    public function withMessage(object $message): Envelope;

    /**
     * A new instance of the same class, with a serialized message of the same type.
     */
    public function withSerializedMessage(string $serializedMessage): Envelope;

    /**
     * @throws LogicException When the serialized message has not been provided
     */
    public function serializedMessage(): string;
}
