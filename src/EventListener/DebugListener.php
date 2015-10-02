<?php

namespace SimpleBus\BernardBundleBridge\EventListener;

use Bernard\Command\ConsumeCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DebugListener implements EventSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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

        /* @var \Doctrine\DBAL\Connection $db */
        $db = $this->container->get('doctrine.dbal.default_connection');

        $db->getConfiguration()->setSQLLogger(null);
    }
}
