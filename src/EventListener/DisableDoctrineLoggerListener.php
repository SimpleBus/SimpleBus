<?php

namespace SimpleBus\BernardBundleBridge\EventListener;

use Bernard\Command\ConsumeCommand;
use Doctrine\Common\Persistence\ConnectionRegistry;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DisableDoctrineLoggerListener implements EventSubscriberInterface
{
    private $doctrine;

    public function __construct(ConnectionRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'onCommand',
        ];
    }

    public function onCommand(ConsoleEvent $event)
    {
        $command = $event->getCommand();

        if (!$command instanceof ConsumeCommand) {
            return;
        }

        foreach ($this->doctrine->getConnections() as $conn) {
            /* @var \Doctrine\DBAL\Connection $conn */
            $conn->getConfiguration()->setSQLLogger(null);
        }
    }
}
