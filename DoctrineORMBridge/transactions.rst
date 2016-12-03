Transactions
============

It is generally a good idea to wrap command handling in a database
transaction. If you want to do this, add the
``WrapsMessageHandlingInTransaction`` middleware to the command bus.
Provide an instance of the Doctrine ``ManagerRegistry`` interface and
the name of the entity manager that you want to use.

.. code:: php

    use SimpleBus\DoctrineORMBridge\MessageBus\WrapsMessageHandlingInTransaction;

    /*
     * $managerRegistry is an instance of Doctrine\Common\Persistence\ManagerRegistry
     *
     * For example: if you use Symfony, use the "doctrine" service
     */
    $managerRegistry = ...;

    $transactionalMiddleware = new WrapsMessageHandlingInTransaction($managerRegistry, 'default');

    $commandBus->addMiddleware($transactionalMiddleware);

    .. rubric:: Don't call ``flush()`` yourself
       :name: dont-call-flush-yourself

    Once you have added this middleware, you shouldn't call
    ``EntityManager::flush()`` manually from inside your command
    handlers anymore.
