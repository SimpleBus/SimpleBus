<?php

namespace SimpleBus\RabbitMQBundle\ErrorHandling;

use Assert\Assertion;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class DelegatingErrorHandler implements ErrorHandler
{
    /**
     * @var ErrorHandler[]
     */
    private $errorHandlers;

    public function __construct(array $errorHandlers)
    {
        Assertion::allIsInstanceOf($errorHandlers, 'SimpleBus\RabbitMQBundle\ErrorHandling\ErrorHandler');
        $this->errorHandlers = $errorHandlers;
    }

    /**
     * Pass the error to all known error handlers
     *
     * @{inheritdoc}
     * @param AMQPMessage $message
     * @param Exception $exception
     */
    public function handle(AMQPMessage $message, Exception $exception)
    {
        foreach ($this->errorHandlers as $errorHandler) {
            $errorHandler->handle($message, $exception);
        }
    }
}
