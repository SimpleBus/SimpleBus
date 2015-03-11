---
currentMenu: error_handling
---

# Error handling

When an exception is thrown while a `Message` is being consumed, the exception is not allowed to bubble up so it won't
cause the consumer process to fail. That way, one `Message` that can't be processed is no danger to any other `Message`.

The AMQP message containing the `Message` that caused the failure will be logged, together with the `Exception` that was
thrown.

If you want to implement some other error handling behaviour (e.g. storing the message to be published again later), you
only need to implement the `ErrorHandler` interface:

```php
use SimpleBus\RabbitMQBundle\ErrorHandling\ErrorHandler;
use PhpAmqpLib\Message\AMQPMessage;
use Exception;

class MyErrorHandler implements ErrorHandler
{
    public function handle(AMQPMessage $message, Exception $exception)
    {
        ...
    }
}
```

Now register your error handler as a service with the tag `simple_bus.rabbit_mq.error_handler`:

```yaml
services:
    my_error_handler:
        class: MyErrorHandler
        tags:
            - { name: simple_bus.rabbit_mq.error_handler }
```
