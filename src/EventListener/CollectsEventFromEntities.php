<?php

namespace SimpleBus\DoctrineORMBridge\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use SimpleBus\Event\Provider\ProvidesEvents;

class CollectsEventFromEntities implements EventSubscriber, ProvidesEvents
{
    private $collectedEvents = array();

    public function getSubscribedEvents()
    {
        return array(
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        );
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->collectEventsFromEntity($event);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->collectEventsFromEntity($event);
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        $this->collectEventsFromEntity($event);
    }

    public function releaseEvents()
    {
        $events = $this->collectedEvents;

        $this->clear();

        return $events;
    }

    private function collectEventsFromEntity(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof ProvidesEvents) {
            foreach ($entity->releaseEvents() as $event) {
                $this->collectedEvents[] = $event;
            }
        }
    }

    private function clear()
    {
        $this->collectedEvents = array();
    }
}
