---
currentMenu: events
---

# Events

## Failure during message consumption

When an exception is thrown while a `Message` is being consumed, the exception is not allowed to bubble up so it won't
cause the consumer process to fail. That way, one `Message` that can't be processed is no danger to any other `Message`.

The AMQP message containing the `Message` that caused the failure will be logged, together with the `Exception` that was
thrown.

If you want to implement some other error handling behaviour (e.g. storing the message to be published again later), you
only need to implement an event subscriber (or listener if you want to) which subscribes to the event
`simple_bus.rabbit_mq_bundle_bridge.message_consumption_failed`:

```php
use SimpleBus\RabbitMQBundleBridge\Event\Events;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumptionFailed;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MyErrorHandler implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [Events::MESSAGE_CONSUMPTION_FAILED => 'messageConsumptionFailed'];
    }

    public function messageConsumptionFailed(MessageConsumptionFailed $event)
    {
        $exception = $event->exception();
        $amqpMessage = $event->message();
        ...
    }
}
```

Don't forget to define a service for it and tag it as `kernel.event_subscriber`:

```yaml
services:
    my_error_handler:
        class: MyErrorHandler
        tags:
            - { name: kernel.event_subscriber }
```

## Successful message consumption

When a `Message` has been handled successfully you may want to perform some additional actions. You can do this by
creating an event subscriber which subscribes to the `simple_bus.rabbit_mq_bundle_bridge.message_consumed` event:


```php
use SimpleBus\RabbitMQBundleBridge\Event\Events;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumed;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MySuccessHandler implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [Events::MESSAGE_CONSUMED => 'messageConsumed'];
    }

    public function messageConsumed(MessageConsumed $event)
    {
        $amqpMessage = $event->message();
        ...
    }
}
```

Don't forget to define a service for it and tag it as `kernel.event_subscriber`:

```yaml
services:
    my_success_handler:
        class: MySuccessHandler
        tags:
            - { name: kernel.event_subscriber }
```
