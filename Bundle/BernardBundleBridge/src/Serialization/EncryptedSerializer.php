<?php

namespace SimpleBus\BernardBundleBridge\Serialization;

use SimpleBus\BernardBundleBridge\Encrypter\Encrypter;
use SimpleBus\Serialization\ObjectSerializer;

class EncryptedSerializer implements ObjectSerializer
{
    private $serializer;
    private $encrypter;

    public function __construct(ObjectSerializer $serializer, Encrypter $encrypter)
    {
        $this->serializer = $serializer;
        $this->encrypter = $encrypter;
    }

    public function serialize($object)
    {
        $data = $this->serializer->serialize($object);

        return $this->encrypter->encrypt($data);
    }

    public function deserialize($encrypted, $type)
    {
        $data = $this->encrypter->decrypt($encrypted);

        return $this->serializer->deserialize($data, $type);
    }
}
