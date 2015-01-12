<?php

namespace SimpleBus\DoctrineORMBridge\CommandBus;

use Doctrine\ORM\EntityManager;
use SimpleBus\Command\Bus\Middleware\CommandBusMiddleware;
use SimpleBus\Command\Command;

class WrapsCommandHandlingInTransaction implements CommandBusMiddleware
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(Command $command, callable $next)
    {
        $this->entityManager->transactional(
            function () use ($command, $next) {
                $next($command);
            }
        );
    }
}
