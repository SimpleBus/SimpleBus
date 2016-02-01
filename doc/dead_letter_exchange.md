---
currentMenu: dead_letter_exchange
---

# The infinite loop problem

By default if an command throws an exception while running the messages gets the `reject` status and RabbitMQ will
mark the message as a failure and destroys this message. The reason for this is that RabbitMQ is a FIFO system which
means it will try to handle the messages which where dispatched first as the first message. Even if a message is
rejected by the consumer. If we would requeue the message on failure the failed message will be handled as the next
message in line. This means that your failed message will get in an infinite loop until you stop you queue or deal
with the error. All the other messages coming after the failed message will not be consumed if you have only one
consumer running.

## Dead Letter Exchange

One way to solve failures is to use to use a [Dead Letter Exchange](https://www.rabbitmq.com/dlx.html). A Dead
Letter Exchange is a way to handle failed messages by redirecting them to another exchange and handle them there.
The following is an example of how you can configure a Dead Letter Exchange. The moment we send a `reject` the
message will move to the `asynchronous_commands_fallback` exchange and it will sit there until you start handling
this consumer.

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
        asynchronous_commands_fallback:
            connection:       default
            exchange_options: { name: 'asynchronous_commands_fallback', type: direct }
            queue_options:    { name: 'asynchronous_commands_fallback' }
            # use the consumer provided by SimpleBusRabbitMQBundleBridgeBundle
            callback:         simple_bus.rabbit_mq_bundle_bridge.commands_consumer
        asynchronous_commands:
            connection:       default
            exchange_options: { name: 'asynchronous_commands', type: direct }
            queue_options:    { name: 'asynchronous_commands', arguments: {'x-dead-letter-exchange': ['S', 'asynchronous_commands_fallback']} }
            # use the consumer provided by SimpleBusRabbitMQBundleBridgeBundle
            callback:         simple_bus.rabbit_mq_bundle_bridge.commands_consumer
```

### Manually

One way of dealing with this is manually the reason for this approach is simple you can debug your messages.
One problem with this approach is that you need to start monitoring the queue for failed messages.

### Auto retry

One other way of dealing with this is by using a per message ttl. With the per message ttl you can define how
long a message should stay in a queue until it's marked as a dead letter again and will move back to the active
asynchronous_commands queue. In the example below this is set to 3600000 milliseconds which means it sits there
for 1 hour until it is marked dead and delivered back into the normal asynchronous_commands queue. The only
problem with this is that you still end up in a infinite loop if you don't fix the problem but this is now
delayed and other messages can go trough.

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
        asynchronous_commands_fallback:
            connection:       default
            exchange_options: { name: 'asynchronous_commands_fallback', type: direct }
            queue_options:    { name: 'asynchronous_commands_fallback', arguments: {'x-dead-letter-exchange': ['S', 'asynchronous_commands'], 'x-message-ttl': ['I', 3600000]} }
            # use the consumer provided by SimpleBusRabbitMQBundleBridgeBundle
            callback:         simple_bus.rabbit_mq_bundle_bridge.commands_consumer
        asynchronous_commands:
            connection:       default
            exchange_options: { name: 'asynchronous_commands', type: direct }
            queue_options:    { name: 'asynchronous_commands', arguments: {'x-dead-letter-exchange': ['S', 'asynchronous_commands_fallback']} }
            # use the consumer provided by SimpleBusRabbitMQBundleBridgeBundle
            callback:         simple_bus.rabbit_mq_bundle_bridge.commands_consumer
```
