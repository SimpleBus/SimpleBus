<?php

namespace SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Entity;

use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityAboutToBeRemoved;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityChanged;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityCreated;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityNotDirty;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;

/**
 * @Entity
 */
class EventRecordingEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    private $id;

    /**
     * @Column(type="string")
     */
    private $something;

    public function __construct()
    {
        $this->record(new EntityCreated());

        $this->something = 'initial value';
    }

    public function changeSomething()
    {
        $this->something = 'changed value';

        $this->record(new EntityChanged());
    }

    public function prepareForRemoval()
    {
        $this->something = 'changed for the last time';

        $this->record(new EntityAboutToBeRemoved());
    }

    public function recordMessageWithoutStateChange()
    {
        $this->record(new EntityNotDirty());
    }
}
