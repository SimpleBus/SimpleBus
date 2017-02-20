Getting started with Symfony
============================

Using the Symfony framework will hide some of the complexity compared
to when use are interacting directly with the components. The SymfonyBridge
package contains the following bundles which can be used to
integrate SimpeBus with a Symfony application:

-  :doc:`CommandBusBundle <command-bus-bundle>`.
-  :doc:`EventBusBundle <event-bus-bundle>`.
-  :doc:`DoctrineORMBridgeBundle <doctrine-orm-bridge-bundle>`.

Are you upgrading from a previous version? Read the :doc:`upgrade guide <upgrade-guide>`.


Installation
------------

Download the SymfonyBridge with composer.

.. code-block::  bash

    composer require simple-bus/symfony-bridge

When composer is done you can enable the bundles you want in the AppKernel.php

.. code-block::  php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                //...
                new SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle(),
                new SimpleBus\SymfonyBridge\SimpleBusEventBusBundle(),
                new SimpleBus\SymfonyBridge\DoctrineOrmBridgeBundle(),
            )
            //...
        }
        //...
    }

Read more how you use the bundles in the documentation pages for :doc:`CommandBusBundle <command-bus-bundle>`,
:doc:`EventBusBundle <event-bus-bundle>` and
:doc:`DoctrineORMBridgeBundle <doctrine-orm-bridge-bundle>`.
