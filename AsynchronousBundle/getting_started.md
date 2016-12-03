---
currentMenu: getting_started
---

# Getting started

## Introduction

This bundle defines a new command and event bus, to be used for processing asynchronous commands and events. It
also adds middleware to existing the command and event buses which publishes messages to be processed asynchronously.
See also the documentation of [SimpleBus/Asynchronous](http://simplebus.github.io/Asynchronous).

First, enable the `SimpleBusAsynchronousBundle` in your `AppKernel` class.

## Provide an object serializer

The first thing you need to do is to provide a service that is able to serialize any object. This service needs to
implement `SimpleBus\Serialization\ObjectSerializer`.

```yaml
# in config.yml
simple_bus_asynchronous:
    object_serializer_service_id: your_object_serializer
```

> ## Use an existing object serializer
>
> Instead of creating your own object serializer, you should install the
> [SimpleBus/JMSSerializerBundle](https://github.com/SimpleBus/JMSSerializerBundle). Once you register this bundle in
> your `AppKernel` as well, it will automatically register itself as the preferred object serializer. So if you do,
> don't forget to remove the key `simple_bus_asynchronous.object_serializer_service_id` from your config file.

## Provide message publishers

Next, you need to define services that are able to publish commands and events, for example to some message queue.
These services should both implement `SimpleBus\Asynchronous\Publisher\Publisher`. When you have defined them as
services, mention their service id in the configuration:

```yaml
# in config.yml
simple_bus_asynchronous:
    commands:
        publisher_service_id: your_command_publisher
    events:
        publisher_service_id: your_event_publisher
```

> ## Use existing publishers
>
> Instead of writing your own publishers, you can use existing publisher implementations.
>
> As part of SimpleBus a [RabbitMQBundle](https://github.com/SimpleBus/RabbitMQBundle) has been provided which
> automatically registers command and event publishers to publish serialized messages to a RabbitMQ exchange.

## Logging

To get some insight into what goes on in the consumer process, enable logging:

```yaml
# in config.yml
simple_bus_asynchronous:
    commands:
        ...
        logging: ~
    events:
        ...
        logging: ~
```

This will log consumed messages to the `asynchronous_command_bus` and `asynchronous_event_bus` channels respectively.

## Choose event strategy

When handling events you have two predefined strategies to choose from. Either you publish *all* events to the message
queue (*always* strategy) or you only publish the events that have a registered asynchronous subscriber
(*predefined* strategy). If your application is the only one that is consuming messages you should consider using the
**predefined** strategy. This will reduce the message overhead on the message queue.

```yaml
simple_bus_asynchronous:
  events:
    strategy: 'predefined' # default: 'always'
```

You can also use Your own strategy by defining custom **strategy_service_id**

```yaml
simple_bus_asynchronous:
  events:
    strategy:
      strategy_service_id: your_strategy_service
```
