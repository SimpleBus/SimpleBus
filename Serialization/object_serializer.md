---
currentMenu: object_serializer
---

# Object serializer

An object serializer is supposed to be able to serialize *any object* handed to it. `SimpleBus/Serializer` contains a
simple implementation of an object serializer, which uses the native PHP `serialize()` and `unserialize()` functions:

```php
// $envelope is an instance of Envelope, containing a serialized message
$envelope = ...;

$serializer = NativeObjectSerializer();
$serializedEnvelope = $serializer->serialize($envelope);

$deserializedEnvelope = $serializer->deserialize($serializedEnvelope, get_class($envelope));
```

> ## Use another serializer
>
> You are encouraged to use a more advanced serializer like the
[JMSSerializer](https://github.com/schmittjoh/serializer).
[SimpleBus/JMSSerializerBridge](https://github.com/SimpleBus/JMSSerializerBridge) contains an adapter for the SimpleBus
`ObjectSerializer` interface.
>
> Using JSON or XML as the serialized format a message is better readable and understandable for humans, but more
importantly, it's platform-independent.
