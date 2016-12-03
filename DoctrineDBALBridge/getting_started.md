---
currentMenu: getting_started
---

# Getting started

## Installation

Using [Composer](https://getcomposer.org/):

```bash
composer require simple-bus/doctrine-dbal-bridge
```

## Preparations

To use the middleware provided by the library,
set up a [command bus](http://simplebus.github.io/MessageBus/doc/command_bus.html),
if you didn't already do this:

```php
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;

$commandBus = new MessageBusSupportingMiddleware();
...
```

Make sure to also properly set up a Doctrine connection:

```php
// $connection is an instance of Doctrine\DBAL\Driver\Connection
$connection = ...;
```

Now add the available middleware for [transaction handling](transactions.md).
