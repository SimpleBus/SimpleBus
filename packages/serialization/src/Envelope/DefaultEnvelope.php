<?php

namespace SimpleBus\Serialization\Envelope;

use LogicException;

class DefaultEnvelope implements Envelope
{
    /**
     * @var class-string
     */
    private string $messageType;

    private ?object $message;

    private ?string $serializedMessage;

    /**
     * @param class-string $messageType
     */
    protected function __construct(string $messageType, ?object $message, ?string $serializedMessage)
    {
        $this->messageType = $messageType;
        $this->message = $message;
        $this->serializedMessage = $serializedMessage;
    }

    public static function forMessage(object $message): self
    {
        $type = get_class($message);

        return new self($type, $message, null);
    }

    /**
     * @param class-string $type
     */
    public static function forSerializedMessage(string $type, string $serializedMessage): self
    {
        return new self($type, null, $serializedMessage);
    }

    public function messageType(): string
    {
        return $this->messageType;
    }

    public function message(): object
    {
        if (null === $this->message) {
            throw new LogicException('Message is unavailable');
        }

        return $this->message;
    }

    public function serializedMessage(): string
    {
        if (null === $this->serializedMessage) {
            throw new LogicException('Serialized message is unavailable');
        }

        return $this->serializedMessage;
    }

    public function withMessage(object $message): self
    {
        return new self($this->messageType, $message, $this->serializedMessage);
    }

    public function withSerializedMessage(string $serializedMessage): self
    {
        return new self($this->messageType, $this->message, $serializedMessage);
    }
}
