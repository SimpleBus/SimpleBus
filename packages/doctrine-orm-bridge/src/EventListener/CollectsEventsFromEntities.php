<?php

namespace SimpleBus\DoctrineORMBridge\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Proxy\Proxy;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;

class CollectsEventsFromEntities implements EventSubscriber, ContainsRecordedMessages
{
    /**
     * @var object[]
     */
    private array $collectedEvents = [];

    public function getSubscribedEvents(): array
    {
        return [
            Events::preFlush,
            Events::postFlush,
        ];
    }

    public function preFlush(PreFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getIdentityMap() as $entities) {
            foreach ($entities as $entity) {
                $this->collectEventsFromEntity($entity);
            }
        }
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $this->collectEventsFromEntity($entity);
        }
    }

    /**
     * We need to listen on postFlush for Lifecycle Events
     * All Lifecycle callback events are triggered after the onFlush event.
     */
    public function postFlush(PostFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getIdentityMap() as $entities) {
            foreach ($entities as $entity) {
                $this->collectEventsFromEntity($entity);
            }
        }
    }

    /**
     * @return object[]
     */
    public function recordedMessages(): array
    {
        return $this->collectedEvents;
    }

    public function eraseMessages(): void
    {
        $this->collectedEvents = [];
    }

    private function collectEventsFromEntity(object $entity): void
    {
        if ($entity instanceof ContainsRecordedMessages
            && (
                !$entity instanceof Proxy
                || ($entity instanceof Proxy && $entity->__isInitialized__)
            )
        ) {
            foreach ($entity->recordedMessages() as $event) {
                $this->collectedEvents[] = $event;
            }
            $entity->eraseMessages();
        }
    }
}
