# Cookbook

## Setting up SQS

Install [AWS SDK for PHP](https://aws.amazon.com/sdk-for-php/) and register _SQS_ client service in container. Then you can do something like this:

```yaml:
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
```

## Setting up failure queue

While consuming a message handler can throw an exception, thus leaving the message unacknowledged. In drivers like SQS this will result in same message being processed over and over again. To overcome this you can re-route all rejected messages to another queue for later evaluation:

```yaml
bernard:
    listeners:
        failure: failures
```

Bernard will catch an exception thrown by a handler, acknowledge a message and re-route to the `failures` queue. 

## Custom SimpleBus publisher

_SimpleBus_ always publishes events when asynchronous events are enabled. This is because `AlwaysPublishesMessages` publisher is used for events (more info [here](http://simplebus.github.io/Asynchronous/doc/publishing_messages.html)).

Sometimes this is not what you want as it is impossible to mix sync and async events. In other words synchronous events are published to the queue even if you don't intend to process them asynchronously.

To overcome this a custom event publisher can be implementd. Consider the following example:

```php
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
```

Register your custom publisher:

```yaml
services:
    my.simple_bus.event_publisher:
        class: My\AppBundle\SimpleBus\MyEventPublisher
        arguments: [@simple_bus.bernard_bundle_bridge.event_publisher]
```

Update _SimpleBus_ config:

```yaml
simple_bus_asynchronous:
    events:
        publisher_service_id: my.simple_bus.event_publisher
```

From now on only events with `@ExclusionPolicy` docblock will be processed asynchronously.

Example of async event:

```php
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

```

