Installation
============

Add the following to your composer.json:

.. code:: javascript

    {
        "require": {
            "simple-bus/bernard-bundle-bridge": "~1.0",
            "simple-bus/jms-serializer-bundle-bridge": "~2.0"
        }
    }

The *jms-serializer* is recommended, but not required. Read
`here <http://simplebus.github.io/AsynchronousBundle/doc/getting_started.html>`__
how to register your own serializer.

**Note**

This integration relies on development versions of *Bernard* and
*BernardBundle*. Latest *Bernard* is quite different from a stable
release. Unfortunately nor latest *BernardBundle* iteration neither the
library itself is tagged appropriately. If you don't allow dev packages
in your *composer.json* with ``minimum-stability`` option (which is a
right thing to do), then add below lines to your composer.json as well:

.. code:: javascript

    {
        "require": {
            ...
            
            "bernard/bernard-bundle": "2.0.*@dev",
            "bernard/bernard": "1.0.*@dev"
        }
    }

I believe this requirement is temporary and will be solved eventually by
*Bernard* contributors.

Register bundle in the kernel:

.. code:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            
            // Bernard
            new \Bernard\BernardBundle\BernardBundle(),

            // SimpleBus integration
            new \SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle(),
            new \SimpleBus\SymfonyBridge\SimpleBusEventBusBundle(),
            new \SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle(),
            new \SimpleBus\JMSSerializerBundleBridge\SimpleBusJMSSerializerBundleBridgeBundle(),
            
            // This bundle
            new \SimpleBus\BernardBundleBridge\SimpleBusBernardBundleBridgeBundle(),
        );
    }

Configuration
-------------

Choose *Bernard* driver:

.. code:: yaml

    bernard:
        driver: doctrine

More info about *Bernard*
`configuration <https://github.com/bernardphp/BernardBundle>`__.

Async commands
''''''''''''''

Minimal config to enable asynchronous commands:

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        commands: ~

All asynchronous commands will be routed to ``asynchronous_commands``
queue.

Async events
''''''''''''

Minimal config to enable asynchronous events:

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        events: ~

All asynchronous events will be routed to ``asynchronous_events`` queue.

Consuming messages
------------------

To consume *SimpleBus* messages in *Bernard*, please, run:

.. code:: bash

    $ # Consume commands
    $ ./app/console bernard:consume asynchronous_commands
    $
    $ # Consume events
    $ ./app/console bernard:consume asynchronous_events

Next
----

Read next about
`routing <https://github.com/SimpleBus/SimpleBusBernardBundleBridge/blob/master/doc/routing.md>`__
options.
