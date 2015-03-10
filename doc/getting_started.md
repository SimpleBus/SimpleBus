---
currentMenu: getting_started
---

# Using RabbitMQ for publishing messages

The `SimpleBusRabbitMQBundle` allows you to publish and consume SimpleBus messages using RabbitMQ.

## Getting started

First, enable [SimpleBusAsynchronousBundle](https://github.com/SimpleBus/AsynchronousBundle) in your Symfony project.
Next enable `SimpleBusRabbitMQBundle` and [OldSoundRabbitMqBundle](https://github.com/videlalvaro/RabbitMqBundle).

### Handling commands asynchronously

If you want commands to be handled asynchronously, you should first configure `OldSoundRabbitMqBundle`:

```yaml
# in config.yml
old_sound_rabbit_mq:
    # don't forget to provide the connection details
    ...
    producers:
        ...
        asynchronous_commands:
            connection:       default
            exchange_options: { name: 'asynchronous_commands', type: direct }
    consumers:
        ...
        asynchronous_commands:
            connection:       default
            exchange_options: { name: 'asynchronous_commands', type: direct }
            queue_options:    { name: 'asynchronous_commands' }
            # use the consumer provided by SimpleBusRabbitMQBundle
            callback:         simple_bus.rabbit_mq.commands_consumer
```

Now enable asynchronous command handling:

```yaml
# in config.yml

simple_bus_rabbit_mq:
    commands:
        # this producer service will be defined by OldSoundRabbitMqBundle,
        # its name is old_sound_rabbit_mq.%producer_name%_producer
        producer_service_id: old_sound_rabbit_mq.asynchronous_commands_producer
```

Please note that commands are only handled asynchronously when there is no regular handler defined for it. Instead of
registering the handler using the tag `command_handler`, you should now register it using the tag
`asynchronous_command_handler`:

```yaml
services:
    my_asynchronous_command_handler:
        class: ...
        tags:
            { name: asynchronous_command_handler, handles: ... }
```

See also the documentation of [SimpleBus/AsynchronousBundle](https://github.com/SimpleBus/AsynchronousBundle).

To actually consume command messages, you need to start (and keep running):

    php app/console rabbitmq:consume asynchronous_commands

### Handling events asynchronously

If you want events to be handled asynchronously, you should first configure `OldSoundRabbitMqBundle`:

```yaml
# in config.yml
old_sound_rabbit_mq:
    # don't forget to provide the connection details
    ...
    producers:
        ...
        asynchronous_events:
            connection:       default
            exchange_options: { name: 'asynchronous_events', type: direct }
    consumers:
        asynchronous_events:
            connection:       default
            exchange_options: { name: 'asynchronous_events', type: direct }
            queue_options:    { name: 'asynchronous_events' }
            # use the consumer provided by SimpleBusRabbitMQBundle
            callback:         simple_bus.rabbit_mq.asynchronous_events
```

Now enable asynchronous event handling:

```yaml
# in config.yml

simple_bus_rabbit_mq:
    events:
        # this producer service will be defined by OldSoundRabbitMqBundle,
        # its name is old_sound_rabbit_mq.%producer_name%_producer
        producer_service_id: old_sound_rabbit_mq.asynchronous_events_producer
```

Events are *always handled synchronously as well as asynchronously*. If you want an event subscriber to only be notified
of an event asynchronously, instead of registering the subscriber using the tag `event_subscriber` tag, you should now
use the `asynchronous_event_subscriber` tag:

```yaml
services:
    my_asynchronous_event_subscriber:
        class: ...
        tags:
            { name: asynchronous_event_subscriber, subscribes_to: ... }
```

To actually consume event messages, you need to start (and keep running):

    php app/console rabbitmq:consume asynchronous_events

> ### Tweak the configuration
>
> You are encouraged to tweak the exchange/queue options and make them right for your project. Read more about your
> options in the [RabbitMQ documentation](http://www.rabbitmq.com/documentation.html) and in the [documentation of
> OldSoundRabbitMQBundle](https://github.com/videlalvaro/RabbitMqBundle).
