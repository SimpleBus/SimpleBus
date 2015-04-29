<?php

namespace SimpleBus\Serialization\Envelope;

use Assert\Assertion;

class DefaultEnvelope implements Envelope
{
    /**
     * @var string
     */
    private $messageType;

    /**
     * @var object|null
     */
    private $message;

    /**
     * @var string|null
     */
    private $serializedMessage;

    protected function __construct($messageType, $message = null, $serializedMessage = null)
    {
        $this->setMessageType($messageType);
        $this->setMessage($message);
        $this->setSerializedMessage($serializedMessage);
    }

    /**
     * @{inheritdoc}
     */
    public static function forMessage($message)
    {
        $type = get_class($message);

        return new self($type, $message, null);
    }

    /**
     * @{inheritdoc}
     */
    public static function forSerializedMessage($type, $serializedMessage)
    {
        Assertion::string($type);
        Assertion::string($serializedMessage);

        return new self($type, null, $serializedMessage);
    }

    /**
     * @{inheritdoc}
     */
    public function messageType()
    {
        return $this->messageType;
    }

    /**
     * @{inheritdoc}
     */
    public function message()
    {
        if ($this->message === null) {
            throw new \LogicException('Message is unavailable');
        }

        return $this->message;
    }

    /**
     * @{inheritdoc}
     */
    public function serializedMessage()
    {
        if ($this->serializedMessage === null) {
            throw new \LogicException('Serialized message is unavailable');
        }

        return $this->serializedMessage;
    }

    /**
     * @{inheritdoc}
     */
    public function withMessage($message)
    {
        Assertion::isObject($message);

        return new self($this->messageType, $message, $this->serializedMessage);
    }

    /**
     * @{inheritdoc}
     */
    public function withSerializedMessage($serializedMessage)
    {
        Assertion::string($serializedMessage);

        return new self($this->messageType, $this->message, $serializedMessage);
    }

    private function setMessageType($messageType)
    {
        Assertion::string($messageType);

        $this->messageType = $messageType;
    }

    private function setMessage($message = null)
    {
        $this->message = $message;
    }

    private function setSerializedMessage($serializedMessage)
    {
        Assertion::nullOrString($serializedMessage);

        $this->serializedMessage = $serializedMessage;
    }
}
