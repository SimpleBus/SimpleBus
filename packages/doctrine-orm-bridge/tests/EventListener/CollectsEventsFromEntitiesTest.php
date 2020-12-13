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

    protected function getEntityDirectories(): array
    {
        return [__DIR__.'/Fixtures/Entity'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventSubscriber = new CollectsEventsFromEntities();
        $this->getEventManager()->addEventSubscriber($this->eventSubscriber);
    }

    /**
     * @test
     */
    public function itCollectsEventsFromPersistedEntitiesAndErasesThemAfterwards()
    {
        $entity = new EventRecordingEntity();

        $this->persistAndFlush($entity);
        $this->assertContainsEquals(new EntityCreated(), $this->eventSubscriber->recordedMessages());

        $this->assertEntityHasNoRecordedEvents($entity);
    }

    /**
     * @test
     */
    public function itCollectsEventsFromModifiedEntitiesAndErasesThemAfterwards()
    {
        $entity = new EventRecordingEntity();
        $this->persistAndFlush($entity);
        $this->eraseRecordedMessages();

        $entity->changeSomething();
        $this->persistAndFlush($entity);

        $this->assertContainsEquals(new EntityChanged(), $this->eventSubscriber->recordedMessages());

        $this->assertEntityHasNoRecordedEvents($entity);
    }

    /**
     * @test
     */
    public function itCollectsEventsFromRemovedEntitiesAndErasesThemAfterwards()
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
    public function itCollectsEventsFromNotDirtyEntitiesAndErasesThemAfterwards()
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
    public function itCollectsEventsFromPrePersistLifecycleCallbacksOfEntitiesAndErasesThemAfterwards()
    {
        $entity = new EventRecordingEntity();
        $this->persistAndFlush($entity);

        $this->assertContainsEquals(new EntityCreatedPrePersist(), $this->eventSubscriber->recordedMessages());

        $this->assertEntityHasNoRecordedEvents($entity);
    }

    /**
     * @test
     */
    public function itCollectsEventsFromPreUpdateLifecycleCallbacksOfDirtyEntitiesAndErasesThemAfterwards()
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
