---
currentMenu: routing
---

# Routing

By default, this bundle assumes that you want to use "direct" exchanges and use one queue for all commands, and one
queue for all events. If you want to use "topic" exchanges and selectively consume messages using a routing key, this
bundle can generate routing keys automatically for you based on the class name of the `Message`. Just change the
bundle configuration:

```yaml
# in config.yml
simple_bus_rabbit_mq:
    routing_key: class_based
```

When for example a `Message` of class `Acme\Command\RegisterUser` is published to the queue, its routing key will be
`Acme.Command.RegisterUser`. Now you can define consumers for specific messages, based on this routing key:

```yaml
# in config.yml
old_sound_rabbit_mq:
    ...
    consumers:
        acme_commands:
            connection:       default
            exchange_options: { name: 'asynchronous_commands', type: topic }
            queue_options:    { name: 'asynchronous_commands', routing_keys: ['Acme.Command.#'] }
            callback:         simple_bus.rabbit_mq.events_consumer
```

> ## Fair dispatching
>
> If you are looking for a way to evenly distribute messages over several workers, you may not be better off using a
> "topic" exchange. Instead, you could just use a "direct" exchange, spin up several workers, and configure
> consumers to prefetch only one message at a time:
>
> ```yaml
> # in config.yml
> old_sound_rabbit_mq:
>     consumers:
>         ...
>         asynchronous_commands:
>             ...
>             qos_options:
>                 prefetch_count: 1
> ```
>
> See also [Fair dispatching](https://github.com/videlalvaro/RabbitMqBundle#fair-dispatching) in the bundle's official
> documentation.
