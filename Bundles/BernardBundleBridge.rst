BernardBundleBridge
===================

`Bernard <https://github.com/bernardphp/bernard>`__ integration with
`SimpleBus <http://simplebus.github.io/MessageBus>`__ via
`BernardBundle <https://github.com/bernardphp/BernardBundle>`__ i.e.
ability to deliver SimpleBus (encrypted) messages with Bernard.

Installation
------------

Add the following to your composer.json:

.. code:: bash

    composer require simple-bus/bernard-bundle-bridge
    composer require simple-bus/jms-serializer-bundle-bridge

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

.. code:: bash

    composer require bernard/bernard-bundle@dev
    composer require bernard/bernard@dev

I believe this requirement is temporary and will be solved eventually by
*Bernard* contributors.

Register bundle in the kernel:

.. code:: php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                ...
                // Bernard
                new Bernard\BernardBundle\BernardBundle(),

                // SimpleBus integration
                new SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle(),
                new SimpleBus\SymfonyBridge\SimpleBusEventBusBundle(),
                new SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle(),
                new SimpleBus\JMSSerializerBundleBridge\SimpleBusJMSSerializerBundleBridgeBundle(),

                // This bundle
                new SimpleBus\BernardBundleBridge\SimpleBusBernardBundleBridgeBundle(),
                ...
            );
        }
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

    # Consume commands
    ./app/console bernard:consume asynchronous_commands

    # Consume events
    ./app/console bernard:consume asynchronous_events

Routing
-------

Customizing queue names
.......................

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        commands: my_queue_for_commands
        events: my_queue_for_events

All commands and events will be routed to *my\_queue\_for\_commands* and
*my\_queue\_for\_events* respectively.

Resolving queue name automatically
..................................

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
.............................

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
.....................

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

Consuming messages
------------------

To consume messages in the queue run the following:

.. code:: bash

    ./app/console bernard:consume <queue-name>

Above will start a PHP process (essentially a loop), looking up for the
messages in specified queue. To end the process press ``CTLR+c``.

PHP is meant to die, hence it is not recommended to rely on endless
``bernard:consume`` execution. Especially when you deal with Doctrine,
filling it's identity map with objects, thus consuming more and more
memory. Extra care must be taken to clear *EntityManager* approprietly,
make sure garbage collector is executed by running
``gc_collect_cycles()`` function etc. Unless you know what you're doing
it is expected for ``bernard:consume`` to exit.

If you don't want to deal with this yourself, you can enable the
`LongRunningBundle <https://github.com/LongRunning/LongRunning>`__ to
automatically cleanup after a message is consumed.

Using cron
..........

Below example consumes messages for 5 minutes and exits:

.. code:: bash

    */5 * * * * /var/www/symfony/app/console bernard:consume --max-runtime=300 >> /var/log/symfony/cron.log 2>&1

In other words, a cron job is run in 5 minutes interval consuming
messages during 5 minutes i.e. there is always an active process.

When amount of incoming messages is low, you could do something like
this:

.. code:: bash

    */3 * * * * /var/www/symfony/app/console bernard:consume --max-messages=90 >> /var/log/symfony/cron.log 2>&1

Consume 90 messages once per 3 minutes. You need to make sure your app
can process 30 messages per minute. Adjust amount of messages and time
to process it accordingly.

Using supervisor
................

The best way to keep the process alive is with
`supervisor <http://supervisord.org>`__.

Consider below example:

.. code:: ini

    [program:geo_location]
    directory   = /var/www/symfony
    user        = symfony
    command     = ./app/console bernard:consume --max-runtime=300 geo_location
    autorestart = true

    [program:mailer_webhook]
    directory   = /var/www/symfony
    user        = symfony
    command     = ./app/console bernard:consume --max-runtime=300 mailer_webhook
    autorestart = true

    [program:mailer_delivery]
    directory    = /var/www/symfony
    user         = symfony
    command      = ./app/console bernard:consume --max-runtime=300 mailer_delivery
    autorestart  = true
    numprocs     = 2
    process_name = %(program_name)s_%(process_num)01d

    [group:bernard]
    programs = geo_location,mailer_webhook,mailer_delivery

Starting *Bernard* processes:

.. code:: bash

    sudo supervisorctl start bernard:*

Above will spawn 4 ``bernard:consume`` instances. 1 process for
*geo\_location* and *mailer\_webhook* and 2 processes for
*mailer\_delivery*. The latter queue is processed faster as two workers
deal with it.

Features
--------

Encryption
..........

*SimpleBusBernardBundleBridge* supports messages encryption. This is
useful when transfering sensitive data using some 3rd party service or
over unencrypted channel.

Minimal configuration to enable encryption is as follows:

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        encryption: ~

By default *nelmio* encrypter is used. This requires *mcrypt* PHP
extension to be installed. You can also adjust a secret key and
encryption algorithm:

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        encryption:
            encrypter: nelmio # default
            secret: my_secret # default: %kernel.secret%
            algorithm: des    # default: rijndael-128

Alternative lightweight ``rot13`` encrypter is supported, however not
recommended for production use. Custom encrypter service is available as
well by implementing
``SimpleBus\BernardBundleBridge\Encrypter\Encrypter`` interface:

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        encryption:
            encrypter: my_encrypter

Logging
.......

You can enable logger listener to debug messages production, consumption
and rejection. Consider below example in development config:

.. code:: yaml

    # config_dev.yml

    monolog:
        channels: [ bernard ]

        handlers:
            ...

            bernard:
                type:     stream
                path:     "%kernel.logs_dir%/bernard.%kernel.environment%.log"
                level:    info
                channels: [ bernard ]

    simple_bus_bernard_bundle_bridge:
        logger: monolog.logger.bernard

Then just tail the logs with:

.. code:: bash

    tail -f app/logs/bernard.dev.log

Please, refer to
`BernardBundle <https://github.com/bernardphp/BernardBundle>`__
documention how to implement your own listeners.

Using doctrine driver
.....................

*Bernard* supports ``doctrine`` adapter, which uses SQL tables to store
messages. If this is the case, then *SimpleBusBernardBundleBridge* turns
SQL logging off for all registered *Doctrine* connections when running
``bernard:consume`` console command. It prevents the consume process to
run ouf of memory.

Cookbook
--------

Setting up SQS
..............

Install `AWS SDK for PHP <https://aws.amazon.com/sdk-for-php/>`__ and
register *SQS* client service in container. Then you can do something
like this:

.. code:: yaml:

    bernard:
        driver: sqs
        options:
            sqs_service: my.sqs.client
            sqs_queue_map:
                geo_location:    https://sqs.eu-west-1.amazonaws.com/11111/project-geo-location
                mailer_webhook:  https://sqs.eu-west-1.amazonaws.com/11111/project-mailer-webhook
                mailer_delivery: https://sqs.eu-west-1.amazonaws.com/11111/project-mailer-delivery

    simple_bus_bernard_bundle_bridge:
        commands:
            queue_name_resolver: mapped
            queues_map:
                My\MailerBundle\Model\Command\BounceEmailCommand: mailer_webhook
                My\MailerBundle\Model\Command\OpenEmailCommand:   mailer_webhook
        events:
            queue_name_resolver: mapped
            queues_map:
                My\GeoBundle\Model\Event\LocationUpdatedEvent: geo_location

Setting up failure queue
........................

While consuming a message an appropriate handler can throw an exception,
thus leaving the message unacknowledged. In drivers like *SQS* this will
result in same message being processed over and over again. To overcome
this you can re-route all rejected messages to another queue for later
evaluation:

.. code:: yaml

    bernard:
        listeners:
            failure: failures

Bernard will catch an exception thrown by a handler, acknowledge a
message and re-route to the ``failures`` queue.

Custom SimpleBus publisher
..........................

*SimpleBus* always publishes events when asynchronous events are
enabled. This is because ``AlwaysPublishesMessages`` publisher is used
for events (more info
`here <http://simplebus.github.io/Asynchronous/doc/publishing_messages.html>`__).

Sometimes this is not what you want as it is impossible to mix sync and
async events. In other words synchronous events are published to the
queue even if you don't intend to process them asynchronously.

To overcome this a custom event publisher can be implementd. Consider
the following example:

.. code:: php

    namespace My\AppBundle\SimpleBus;

    use SimpleBus\Asynchronous\Publisher\Publisher;

    class MyEventPublisher implements Publisher
    {
        private $publisher;
        private $cache = [];

        public function __construct(Publisher $publisher)
        {
            $this->publisher = $publisher;
        }

        public function publish($message)
        {
            $class = get_class($message);

            if (!array_key_exists($class, $this->cache)) {
                $docBlock = (new \ReflectionObject($message))->getDocComment();

                $this->cache[$class] = (boolean) preg_match('/@ExclusionPolicy\(/', $docBlock);
            }

            if ($this->cache[$class]) {
                $this->publisher->publish($message);
            }
        }
    }

Register your custom publisher:

.. code:: yaml

    services:
        my.simple_bus.event_publisher:
            class: My\AppBundle\SimpleBus\MyEventPublisher
            arguments: [@simple_bus.bernard_bundle_bridge.event_publisher]

Update *SimpleBus* config:

.. code:: yaml

    simple_bus_asynchronous:
        events:
            publisher_service_id: my.simple_bus.event_publisher

From now on only events with ``@ExclusionPolicy`` docblock will be
processed asynchronously.

Example of async event:

.. code:: php

    namespace My\AwsBundle\Model\Command;

    use JMS\Serializer\Annotation\ExclusionPolicy;
    use JMS\Serializer\Annotation\Type;

    /**
     * @ExclusionPolicy("NONE")
     */
    class RemoveObjectCommand
    {
        /**
         * @Type("string")
         */
        public $bucket;

        /**
         * @Type("string")
         */
        public $key;
    }
