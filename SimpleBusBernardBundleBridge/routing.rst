Routing
=======

Customizing queue names
-----------------------

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        commands: my_queue_for_commands
        events: my_queue_for_events

All commands and events will be routed to *my\_queue\_for\_commands* and
*my\_queue\_for\_events* respectively.

Resolving queue name automatically
----------------------------------

It is a common practice to have a separate queue for each asynchronous
job type.

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        commands:
            queue_name: my_queue_for_commands
            queue_name_resolver: class_based

Let's say you have ``SendEmailCommand`` and ``BounceEmailCommand``.
``SendEmailCommand`` will be routed to *send\_email\_command* queue and
``BounceEmailCommand`` to *bounce\_email\_command* queue.

Same config option works for events.

Map message to queue manually
-----------------------------

Class based approach works fine, when amount of async messages is not
high. Remember, you need to run
``./app/console bernard:consume <queue-name>`` for each queue.
*SimpleBusBernardBundleBridge* supports mapped queue name resolver:

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        commands:
            queue_name: other_messages # Default queue for commands
            queue_name_resolver: mapped
            queues_map:
                My\MailerBundle\Model\Command\BounceEmailCommand: mailer_webhook
                My\MailerBundle\Model\Command\OpenEmailCommand:   mailer_webhook
                My\MailerBundle\Model\Command\SendEmailCommand:   mailer_delivery
                My\MailerBundle\Model\Command\ResendEmailCommand: mailer_delivery
        events:
            queue_name: other_messages # Default queue for events
            queue_name_resolver: mapped
            queues_map:
                My\MediaBundle\Model\Event\MediaCreatedEvent:  media_processor
                My\MediaBundle\Model\Event\FormatCreatedEvent: media_processor

In above example we use 3 queues for 6 async messages. Commands and
events not specified in the map will be routed to *other\_messages*
queue.

Custom queue resolver
---------------------

You can specify your own queue resolver by implementing
``SimpleBus\Asynchronous\Routing\RoutingKeyResolver`` interface.
Register the service in container and update the config:

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        commands:
            queue_name_resolver: my.custom.queue.resolver
        events: queue_for_events

Above example uses custom queue resolver for commands, however, all
events routed to *queue\_for\_events* queue.

Next
----

Read about various ways to
`consume <https://github.com/SimpleBus/SimpleBusBernardBundleBridge/blob/master/doc/consuming.md>`__
async messages.
