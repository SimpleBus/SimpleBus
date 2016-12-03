Domain events
=============

Using the `message recorder
facilities <http://simplebus.github.io/MessageBus/doc/message_recorder.html>`__
from ``SimpleBus/MessageBus`` you can let Doctrine ORM collect domain
events and subsequently let the event bus handle them.

Make sure that your entities implement the ``ContainsRecordedMessages``
interface. Use the ``PrivateMessageRecorderCapabilities`` trait to
conveniently record events from inside the entity:

.. code:: php

    use SimpleBus\Message\Recorder\ContainsRecordedMessages;
    use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;

    class YourEntity implements ContainsRecordedMessages
    {
        use PrivateMessageRecorderCapabilities;

        public function changeSomething()
        {
            // record new events like this:

            $this->record(new SomethingChanged());
        }
    }

Then set up the *event recorder* for Doctrine entities:

.. code:: php

    use SimpleBus\DoctrineORMBridge\EventListener\CollectsEventsFromEntities;

    $eventRecorder = new CollectsEventsFromEntities();

    $entityManager->getConnection()->getEventManager()->addEventSubscriber($eventRecorder);

The event recorder will loop over all the entities that were involved in
the last database transaction and collect their internally recorded
events.

After a database transaction was completed successfully these events
should be handled by the event bus. This is done by a specialized
middleware, which should be appended to the command bus *before* the
middleware that is responsible for handling the transaction.

.. code:: php

    use SimpleBus\DoctrineORMBridge\MessageBus\WrapsMessageHandlingInTransaction;

    use SimpleBus\Message\Bus\MessageBus;

    $eventDispatchingMiddleware = new HandlesRecordedMessagesMiddleware($eventProvider, $eventBus);
    // N.B. append this middleware *before* the WrapsMessageHandlingInTransaction middleware
    $commandBus->appendMiddleware($eventDispatchingMiddleware);

    $transactionalMiddleware = new WrapsMessageHandlingInTransaction($entityManager);
    $commandBus->appendMiddleware($transactionalMiddleware);

    .. rubric:: Prepend middleware
       :name: prepend-middleware

    The ``MessageBusSupportingMiddleware`` class also has a
    ``prependMiddleware()`` method, which you can use to prepend
    middleware instead of appending it.
