---
currentMenu: transactions
---

# Transactions

It is generally a good idea to wrap command handling in a database transaction. If you want to do this, add the
`WrapsMessageHandlingInTransaction` middleware to the command bus. Provide the `EntityManager` instance that you want
to use:

```php
use SimpleBus\DoctrineORMBridge\MessageBus\WrapsMessageHandlingInTransaction;

$transactionalMiddleware = new WrapsMessageHandlingInTransaction($entityManager);

$commandBus->addMiddleware($transactionalMiddleware);
```

> #### Don't call `flush()` yourself
>
> Once you have added this middleware, you shouldn't call `EntityManager::flush()` manually from inside your command
> handlers anymore.
