<?php

namespace SimpleBus\DoctrineORMBridge\CommandBus;

use Doctrine\ORM\EntityManager;
use SimpleBus\Command\Bus\CommandBus;
use SimpleBus\Command\Command;
use SimpleBus\Command\Bus\RemembersNext;

class WrapsNextCommandInTransaction implements CommandBus
{
    use RemembersNext;

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(Command $command)
    {
        $commandBus = $this;

        $this->entityManager->transactional(
            function () use ($commandBus, $command) {
                $commandBus->next($command);
            }
        );
    }
}
