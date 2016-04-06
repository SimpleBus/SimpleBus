---
currentMenu: publishing_messages
---

# Publishing messages

When a `Message` should not be handled by the message bus (i.e. command or event bus) immediately (i.e. synchronously),
it can be *published* to be handled by some other process. This library comes with three strategies for publishing
messages:

1. A message will always *also* be published.
2. A message will only be published when the message bus isn't able to handle it because there is no handler defined for it.
3. A message will be published only if its name exists in a predefined list.

## Strategy 1: Always publish messages

This strategy is very useful when you have an event bus that notifies event subscribers of events that have occurred.
If you have set up the event bus, you can add the `AlwaysPublishesMessages` middleware to it:

```php
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use SimpleBus\Asynchronous\MessageBus\AlwaysPublishesMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\Message;

// $eventBus is an instance of MessageBusSupportingMiddleware
$eventBus = ...;

// $publisher is an instance of Publisher
$publisher = ...;

$eventBus->appendMiddleware(new AlwaysPublishesMessages($publisher));

// $event is an object
$event = ...;

$eventBus->handle($event);
```

The middleware publishes the message to the publisher (which may add it to some a queue of some sorts). After
that it just calls the next middleware and lets it process the same message in the usual way.

By applying this strategy you basically allow other processes to respond to any event that occurs within your
application.

## Strategy 2: Only publish messages that could not be handled

This strategy is useful if you have a command bus that handles commands. If you have set up the command bus, you can add
the `PublishesUnhandledMessages` middleware to it:

```php
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use SimpleBus\Asynchronous\MessageBus\PublishesUnhandledMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

// $commandBus is an instance of MessageBusSupportingMiddleware
$commandBus = ...;

// $publisher is an instance of Publisher
$publisher = ...;

// $logger is an instance of LoggerInterface
$logger = ...;

// $logLevel is one of the class constants of LogLevel
$logLevel = LogLevel::DEBUG;

$commandBus->appendMiddleware(new PublishesUnhandledMessages($publisher, $logger, $logLevel));

// $command is an object
$command = ...;

$commandBus->handle($command);
```

Because of the nature of commands (they have a one-to-one correspondence with their handlers), it doesn't make sense to
always publish a command. Instead, it should only be published when it *couldn't be handled by your application*.
Possibly some other process knows how to handle it.

If no command handler was found and the command is published, this will be logged using the provided `$logger`.

## Strategy 3: Only publish predefined messages

This strategy is useful when you know what messages you want to publish. 

```php
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use SimpleBus\Asynchronous\MessageBus\AlwaysPublishesMessages;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\Message;
use SimpleBus\Message\Name\MessageNameResolver;

// $eventBus is an instance of MessageBusSupportingMiddleware
$eventBus = ...;

// $publisher is an instance of Publisher
$publisher = ...;

// $messageNameResolver is an instance of MessageNameResolver
$messageNameResolver = ...;

// The list of names will depend on what MessageNameResolver you are using. 
$names = ['My\\Event', 'My\\Other\\Event'];

$eventBus->appendMiddleware(new PublishesPredefinedMessages($publisher, $messageNameResolver, $names));

// $event is an object
$event = ...;

$eventBus->handle($event);
```

*Continue reading about [consuming messages](consuming_messages.md)*
