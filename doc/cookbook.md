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

Bernard will catch exception thrown by a handler, acknowledge a message and re-route to the `failures` queue. 
