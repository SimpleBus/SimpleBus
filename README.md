# DoctrineORMBridge

[![Build Status](https://travis-ci.org/matthiasnoback/simple-bus.svg?branch=master)](https://travis-ci.org/matthiasnoback/simple-bus)

By [Matthias Noback](http://php-and-symfony.matthiasnoback.nl/)

## Installation

Using Composer:

    composer require simple-bus/doctrine-orm-bridge

## Usage

1. Set up a [command bus](https://github.com/SimpleBus/CommandBus), an [event bus](https://github.com/SimpleBus/EventBus):

    ```php
    $commandBus = ...;
    $eventBus = ...;
    ```

2. Set up a Doctrine ORM entity manager:

    ```php
    $entityManager = ...;
    ```

3. Create the event provider and register it as a Doctrine event subscriber:

    ```php
    $eventProvider = new CollectsEventFromEntities();

    $entityManager->getConnection()->getEventManager()->addEventSubscriber($eventSubscriber);
    ```

3. If you want your commands to be handled inside a database transaction, wrap the existing command bus:

    ```php
    use SimpleBus\DoctrineORMBridge\CommandBus\WrapsNextCommandInTransaction;

    $transactionalCommandBus = new WrapsNextCommandInTransaction($entityManager);
    $transactionalCommandBus->setNext($commandBus);
    ```

4. If you want to dispatch events collected from the entities that played a part in the last flush operation, wrap the
transactional bus in an event-dispatching command bus:

   ```php
   use SimpleBus\CommandEventBridge\CommandBus\DispatchesEvents;

   $eventDispatchingCommandBus = new DispatchesEvents($eventProvider);
   $eventDispatchingCommandBus->setNext($transactionalCommandBus);
   ```
