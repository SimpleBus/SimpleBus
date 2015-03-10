<?php

namespace SimpleBus\Serialization\Envelope;

use Assert\Assertion;
use SimpleBus\Message\Message;

class DefaultEnvelope implements Envelope
{
    /**
     * @var string
     */
    private $messageType;

    /**
     * @var Message|null
     */
    private $message;

    /**
     * @var string|null
     */
    private $serializedMessage;

    protected function __construct($messageType, Message $message = null, $serializedMessage = null)
    {
        $this->setMessageType($messageType);
        $this->setMessage($message);
        $this->setSerializedMessage($serializedMessage);
    }

    /**
     * @{inheritdoc}
     */
    public static function forMessage(Message $message)
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
    public function withMessage(Message $message)
    {
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
        Assertion::true(
            is_a($messageType, 'SimpleBus\Message\Message', true),
            'The type of the message should be "SimpleBus\Message\Message"'
        );

        $this->messageType = $messageType;
    }

    private function setMessage(Message $message = null)
    {
        $this->message = $message;
    }

    private function setSerializedMessage($serializedMessage)
    {
        Assertion::nullOrString($serializedMessage);

        $this->serializedMessage = $serializedMessage;
    }
}
