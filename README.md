# DoctrineORMBridge

By [Matthias Noback](http://php-and-symfony.matthiasnoback.nl/)

## Installation

Using Composer:

    composer require simple-bus/doctrine-orm-bridge

## Usage

1. Set up a [command bus](https://github.com/SimpleBus/CommandBus):

    ```php
    use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;

    $commandBus = new MessageBusSupportingMiddleware();
    ```

2. Set up a Doctrine ORM entity manager:

    ```php
    // $entityManager is an instance of Doctrine\ORM\EntityManager
    $entityManager = ...;
    ```

3. Create the event provider and register it as a Doctrine event subscriber:

    ```php
    use SimpleBus\DoctrineORMBridge\EventListener\CollectsEventsFromEntities;

    $eventProvider = new CollectsEventsFromEntities();

    $entityManager->getConnection()->getEventManager()->addEventSubscriber($eventProvider);
    ```

3. If you want your commands to be handled inside a database transaction, wrap the existing command bus:

    ```php
    use SimpleBus\DoctrineORMBridge\MessageBus\WrapsMessageHandlingInTransaction;

    $transactionalMiddleware = new WrapsMessageHandlingInTransaction($entityManager);

    $commandBus->addMiddleware($transactionalMiddleware);
    ```

4. If you want to dispatch events collected from the entities that played a part in the last flush operation, register
the event-dispatching middleware. It requires a [`MessageBus`](https://github.com/SimpleBus/MessageBus).

    ```php
    use SimpleBus\Message\Recorder\HandlesRecordedMessagesMiddleware;
    use SimpleBus\Message\Bus\MessageBus;

    // set up an instance of MessageBus
    $eventBus = ...;

    $eventDispatchingMiddleware = new HandlesRecordedMessagesMiddleware($eventProvider, $eventBus);

    // N.B. add this middleware *before* the WrapsMessageHandlingInTransaction middleware
    $commandBus->addMiddleware($eventDispatchingMiddleware);
    ```
