<?php

namespace SimpleBus\DoctrineORMBridge\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;

class CollectsEventsFromEntities implements EventSubscriber, ContainsRecordedMessages
{
    private $collectedEvents = array();

    public function getSubscribedEvents()
    {
        return array(
            Events::preFlush,
        );
    }

    public function preFlush(PreFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getIdentityMap() as $entities) {
            foreach ($entities as $entity){
                $this->collectEventsFromEntity($entity);
            }
        }
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->collectEventsFromEntity($entity);
        }
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $this->collectEventsFromEntity($entity);
        }
    }

    public function recordedMessages()
    {
        return $this->collectedEvents;
    }

    public function eraseMessages()
    {
        $this->collectedEvents = array();
    }

    private function collectEventsFromEntity($entity)
    {
        if ($entity instanceof ContainsRecordedMessages) {
            foreach ($entity->recordedMessages() as $event) {
                $this->collectedEvents[] = $event;
            }
            $entity->eraseMessages();
        }
    }
}
