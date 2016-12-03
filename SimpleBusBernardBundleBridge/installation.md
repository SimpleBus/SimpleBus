# Installation

Add the following to your composer.json:

```javascript
{
    "require": {
        "simple-bus/bernard-bundle-bridge": "~1.0",
        "simple-bus/jms-serializer-bundle-bridge": "~2.0"
    }
}
```

The _jms-serializer_ is recommended, but not required. Read [here](http://simplebus.github.io/AsynchronousBundle/doc/getting_started.html)
how to register your own serializer.

__Note__

This integration relies on development versions of _Bernard_ and _BernardBundle_. Latest _Bernard_ is quite different from a stable release. Unfortunately nor latest _BernardBundle_ iteration neither the library itself is tagged appropriately. If you don't allow dev packages in your _composer.json_ with `minimum-stability` option (which is a right thing to do), then add below lines to your composer.json as well:

```javascript
{
    "require": {
        ...
        
        "bernard/bernard-bundle": "2.0.*@dev",
        "bernard/bernard": "1.0.*@dev"
    }
}
```

I believe this requirement is temporary and will be solved eventually by _Bernard_ contributors.

Register bundle in the kernel:

```php
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
```

## Configuration

Choose _Bernard_ driver:

```yaml
bernard:
    driver: doctrine
```

More info about _Bernard_ [configuration](https://github.com/bernardphp/BernardBundle).

##### Async commands

Minimal config to enable asynchronous commands:

```yaml
simple_bus_bernard_bundle_bridge:
    commands: ~
```

All asynchronous commands will be routed to `asynchronous_commands` queue.

##### Async events

Minimal config to enable asynchronous events:

```yaml
simple_bus_bernard_bundle_bridge:
    events: ~
```

All asynchronous events will be routed to `asynchronous_events` queue.

## Consuming messages

To consume _SimpleBus_ messages in _Bernard_, please, run:

```bash
$ # Consume commands
$ ./app/console bernard:consume asynchronous_commands
$
$ # Consume events
$ ./app/console bernard:consume asynchronous_events
```

## Next

Read next about [routing](https://github.com/SimpleBus/SimpleBusBernardBundleBridge/blob/master/doc/routing.md) options.
