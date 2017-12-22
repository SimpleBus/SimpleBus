---
currentMenu: getting_started
---

# Getting started

## Installation

Using [Composer](https://getcomposer.org/):

```bash
composer require simple-bus/doctrine-orm-bridge
```

## Preparations

To use the middlewares provided by the library, [set up a command
bus](http://simplebus.github.io/MessageBus/doc/command_bus.html) and an [event
bus](http://simplebus.github.io/MessageBus/doc/event_bus.html), if you didn't already do this:

```php
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;

$commandBus = new MessageBusSupportingMiddleware();
...

$eventBus = new MessageBusSupportingMiddleware();
...
```

Make sure to also properly set up an entity manager:

```php
// $entityManager is an instance of Doctrine\ORM\EntityManager
$entityManager = ...;
```

Now add the available middlewares for [transaction handling](transactions.md) and [domain events](domain_events.md).
