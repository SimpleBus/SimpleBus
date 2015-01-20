<?php

namespace SimpleBus\DoctrineORMBridge\Tests\EventListener;

use Noback\PHPUnitTestServiceContainer\PHPUnit\AbstractTestCaseWithEntityManager;
use SimpleBus\DoctrineORMBridge\EventListener\CollectsEventsFromEntities;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Entity\EventRecordingEntity;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityAboutToBeRemoved;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityChanged;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityCreated;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;

class CollectsEventsFromEntitiesTest extends AbstractTestCaseWithEntityManager
{
    /**
     * @var CollectsEventsFromEntities
     */
    private $eventSubscriber;

    protected function getEntityDirectories()
    {
        return array(__DIR__.'/Fixtures/Entity');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->eventSubscriber = new CollectsEventsFromEntities();
        $this->getEventManager()->addEventSubscriber($this->eventSubscriber);
    }

    /**
     * @test
     */
    public function it_collects_events_from_persisted_entities_and_erases_them_afterwards()
    {
        $entity = new EventRecordingEntity();

        $this->persistAndFlush($entity);

        $this->assertEquals([new EntityCreated()], $this->eventSubscriber->recordedMessages());

        $this->assertEntityHasNoRecordedEvents($entity);
    }

    /**
     * @test
     */
    public function it_collects_events_from_modified_entities_and_erases_them_afterwards()
    {
        $entity = new EventRecordingEntity();
        $this->persistAndFlush($entity);
        $this->eraseRecordedMessages();

        $entity->changeSomething();
        $this->persistAndFlush($entity);

        $this->assertEquals([new EntityChanged()], $this->eventSubscriber->recordedMessages());

        $this->assertEntityHasNoRecordedEvents($entity);
    }

    /**
     * @test
     */
    public function it_collects_events_from_removed_entities_and_erases_them_afterwards()
    {
        $entity = new EventRecordingEntity();
        $this->persistAndFlush($entity);
        $this->eraseRecordedMessages();

        $entity->prepareForRemoval();
        $this->removeAndFlush($entity);

        $this->assertEquals([new EntityAboutToBeRemoved()], $this->eventSubscriber->recordedMessages());

        $this->assertEntityHasNoRecordedEvents($entity);
    }

    private function persistAndFlush($entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    private function eraseRecordedMessages()
    {
        $this->eventSubscriber->eraseMessages();
    }

    private function assertEntityHasNoRecordedEvents(ContainsRecordedMessages $entity)
    {
        $this->assertSame([], $entity->recordedMessages());
    }

    private function removeAndFlush($entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
}
