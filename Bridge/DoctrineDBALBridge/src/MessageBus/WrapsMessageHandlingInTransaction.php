<?php

namespace SimpleBus\DoctrineDBALBridge\MessageBus;

use Doctrine\DBAL\Driver\Connection;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;

class WrapsMessageHandlingInTransaction implements MessageBusMiddleware
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        $this->connection->beginTransaction();

        try {
            $next($message);

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollback();

            throw $e;
        }
    }
}
