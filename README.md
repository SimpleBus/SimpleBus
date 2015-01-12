# DoctrineORMBridge

By [Matthias Noback](http://php-and-symfony.matthiasnoback.nl/)

## Installation

Using Composer:

    composer require simple-bus/doctrine-orm-bridge

## Usage

1. Set up a [command bus](https://github.com/SimpleBus/CommandBus):

    ```php
    use SimpleBus\Command\Bus\Middleware\CommandBusSupportingMiddleware;

    $commandBus = new CommandBusSupportingMiddleware();
    ```

2. Set up a Doctrine ORM entity manager:

    ```php
    // $entityManager is an instance of Doctrine\ORM\EntityManager
    $entityManager = ...;
    ```

3. Create the event provider and register it as a Doctrine event subscriber:

    ```php
    $eventProvider = new CollectsEventsFromEntities();

    $entityManager->getConnection()->getEventManager()->addEventSubscriber($eventSubscriber);
    ```

3. If you want your commands to be handled inside a database transaction, wrap the existing command bus:

    ```php
    use SimpleBus\DoctrineORMBridge\CommandBus\WrapsCommandHandlingInTransaction;

    $transactionalMiddleware = new WrapsCommandHandlingInTransaction($entityManager);

    $commandBus->addMiddleware($transactionalMiddleware);
    ```

4. If you want to dispatch events collected from the entities that played a part in the last flush operation, register
the event-dispatching middleware. It requires an [`EventBus`](https://github.com/SimpleBus/EventBus) instance.

    ```php
    use SimpleBus\CommandEventBridge\CommandBus\DispatchesEvents;
    use SimpleBus\Event\Bus\EventBus;

    // set up an instance of EventBus
    $eventBus = ...;

    $eventDispatchingMiddleware = new DispatchesEvents($eventProvider, $eventBus);

    // N.B. add this middleware *before* the WrapsCommandHandlingInTransaction middleware
    $commandBus->addMiddleware($eventDispatchingMiddleware);
    ```
