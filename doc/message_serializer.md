---
currentMenu: message_serializer
---

# Message serializer

In order to to send a `Message` over the network it needs to be wrapped in an `Envelope`. At the other end it may be
unwrapped and processed. This standard procedure is implemented inside the `StandardMessageInEnvelopeSerializer`:

```php
use SimpleBus\Serialization\Envelope\DefaultEnvelopeFactory;
use SimpleBus\Serialization\NativeObjectSerializer;
use SimpleBus\Serialization\Envelope\Serializer\StandardMessageInEnvelopeSerializer;

$envelopeFactory = new DefaultEnvelopeFactory();
$objectSerializer = new NativeObjectSerializer();

$serializer = StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);

// $message is an instance of Message
$message = ...;

// $serializedEnvelope will be a string
$serializedEnvelope = $serializer->wrapAndSerialize($message);

...

// $deserializedEnvelope will be an instance of the original Envelope
$deserializedEnvelope = $serializer->unwrapAndDeserialize($serializedEnvelope);

// $message will be an object which is a copy of the original Message
$message = $deserializedEnvelope->message();
```
