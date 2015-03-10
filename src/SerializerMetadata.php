<?php

namespace SimpleBus\JMSSerializerBridge;

final class SerializerMetadata
{
    public static function directory()
    {
        return __DIR__ . '/metadata';
    }

    public static function namespacePrefix()
    {
        return 'SimpleBus\Serialization';
    }
}
