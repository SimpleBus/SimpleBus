<?php

namespace SimpleBus\DoctrineORMBridge\Tests\EventListener;

use Noback\PHPUnitTestServiceContainer\PHPUnit\TestCaseWithEntityManager;
use PHPUnit\Framework\TestCase;
use SimpleBus\DoctrineORMBridge\EventListener\CollectsEventsFromEntities;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Entity\EventRecordingEntity;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityAboutToBeRemoved;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityChanged;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityChangedPreUpdate;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityCreated;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityCreatedPrePersist;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityNotDirty;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;

class CollectsEventsFromEntitiesTest extends TestCase
{
    use TestCaseWithEntityManager;

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
        $this->assertContains( new EntityCreated(), $this->eventSubscriber->recordedMessages(), '', false, false );

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

        $this->assertContains( new EntityChanged(), $this->eventSubscriber->recordedMessages(), '', false, false );

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

    /**
     * @test
     */
    public function it_collects_events_from_not_dirty_entities_and_erases_them_afterwards()
    {
        $entity = new EventRecordingEntity();
        $this->persistAndFlush($entity);
        $this->eraseRecordedMessages();

        $entity->recordMessageWithoutStateChange();
        $this->persistAndFlush($entity);

        $this->assertEquals([new EntityNotDirty()], $this->eventSubscriber->recordedMessages());

        $this->assertEntityHasNoRecordedEvents($entity);
    }

    /**
     * @test
     */
    public function it_collects_events_from_pre_persist_lifecycle_callbacks_of_entities_and_erases_them_afterwards()
    {
        $entity = new EventRecordingEntity();
        $this->persistAndFlush($entity);

        $this->assertContains( new EntityCreatedPrePersist(), $this->eventSubscriber->recordedMessages(), '', false, false );

        $this->assertEntityHasNoRecordedEvents($entity);
    }

    /**
     * @test
     */
    public function it_collects_events_from_pre_update_lifecycle_callbacks_of_dirty_entities_and_erases_them_afterwards()
    {
        $entity = new EventRecordingEntity();
        $this->persistAndFlush($entity);
        $this->eraseRecordedMessages();

        $entity->changeSomethingWithoutRecording();
        $this->persistAndFlush($entity);

        $this->assertEquals([new EntityChangedPreUpdate()], $this->eventSubscriber->recordedMessages());

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
