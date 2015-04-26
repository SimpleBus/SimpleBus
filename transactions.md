---
currentMenu: transactions
---

# Transactions

It is generally a good idea to wrap command handling in a database transaction. If you want to do this, add the
`WrapsMessageHandlingInTransaction` middleware to the command bus. Provide an instance of the Doctrine `Connection`
interface that you want to use.

```php
use SimpleBus\DoctrineDBALBridge\MessageBus\WrapsMessageHandlingInTransaction;

// $connection is an instance of Doctrine\DBAL\Driver\Connection
$connection = ...;

$transactionalMiddleware = new WrapsMessageHandlingInTransaction($connection);

$commandBus->addMiddleware($transactionalMiddleware);
```

When an exception is thrown, the transaction will be rolled back. If not, the transaction is committed.
