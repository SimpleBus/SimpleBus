<?php

namespace SimpleBus\JMSSerializerBridge;

final class SerializerMetadata
{
    public static function directory(): string
    {
        return __DIR__.'/metadata';
    }

    public static function namespacePrefix(): string
    {
        return 'SimpleBus\Serialization';
    }
}
