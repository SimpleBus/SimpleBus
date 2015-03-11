<?php

namespace SimpleBus\RabbitMQBundle\ErrorHandling;

use Exception;
use PhpAmqpLib\Message\AMQPMessage;

interface ErrorHandler
{
    /**
     * Handle a message that caused an exception while being handled
     *
     * @param AMQPMessage $message
     * @param Exception $exception
     * @return void
     */
    public function handle(AMQPMessage $message, Exception $exception);
}
