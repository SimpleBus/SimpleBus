<?php

namespace SimpleBus\Asynchronous\Message\Envelope;

use Assert\Assertion;
use SimpleBus\Message\Message;

class DefaultEnvelope implements Envelope
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var Message|null
     */
    private $message;

    /**
     * @var string|null
     */
    private $serializedMessage;

    protected function __construct($type, Message $message = null, $serializedMessage = null)
    {
        $this->setType($type);
        $this->setMessage($message);
        $this->setSerializedMessage($serializedMessage);
    }

    public static function forMessage(Message $message)
    {
        $type = get_class($message);

        return new self($type, $message, null);
    }

    public static function forSerializedMessage($type, $serializedMessage)
    {
        Assertion::string($type);
        Assertion::string($serializedMessage);

        return new self($type, null, $serializedMessage);
    }

    public function type()
    {
        return $this->type;
    }

    public function message()
    {
        if ($this->message === null) {
            throw new \LogicException('Message is unavailable');
        }

        return $this->message;
    }

    public function serializedMessage()
    {
        if ($this->serializedMessage === null) {
            throw new \LogicException('Serialized message is unavailable');
        }

        return $this->serializedMessage;
    }

    public function withMessage(Message $message)
    {
        return new self($this->type, $message, $this->serializedMessage);
    }

    public function withSerializedMessage($serializedMessage)
    {
        Assertion::string($serializedMessage);

        return new self($this->type, $this->message, $serializedMessage);
    }

    private function setType($type)
    {
        Assertion::string($type);
        Assertion::true(
            is_a($type, 'SimpleBus\Message\Message', true),
            'The type of the message should be "SimpleBus\Message\Message"'
        );

        $this->type = $type;
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
