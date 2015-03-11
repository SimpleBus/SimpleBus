<?php

namespace SimpleBus\RabbitMQBundle\ErrorHandling;

use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class LoggingErrorHandler implements ErrorHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $logLevel;

    /**
     * @var string
     */
    private $logMessage;

    public function __construct(LoggerInterface $logger, $logLevel, $logMessage)
    {
        $this->logger = $logger;
        $this->logLevel = $logLevel;
        $this->logMessage = $logMessage;
    }

    /**
     * Log the failed message and the related exception
     *
     * @{inheritdoc}
     * @param AMQPMessage $message
     * @param Exception $exception
     */
    public function handle(AMQPMessage $message, Exception $exception)
    {
        $this->logger->log(
            $this->logLevel,
            $this->logMessage,
            [
                'exception' => $exception,
                'message' => $message
            ]
        );
    }
}
