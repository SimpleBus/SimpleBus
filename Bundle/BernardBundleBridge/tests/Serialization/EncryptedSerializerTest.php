<?php

namespace SimpleBus\BernardBundleBridge\Tests\Serialization;

use SimpleBus\BernardBundleBridge\Serialization\EncryptedSerializer;
use SimpleBus\Serialization\NativeObjectSerializer;

class EncryptedSerializerTest extends \PHPUnit\Framework\TestCase
{
    /** @var EncryptedSerializer */
    private $serializer;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $encrypter;

    public function setUp()
    {
        $this->encrypter = $this->createMock('SimpleBus\BernardBundleBridge\Encrypter\Encrypter');
        $this->serializer = new EncryptedSerializer(new NativeObjectSerializer(), $this->encrypter);
    }

    /**
     * @test
     */
    public function it_should_serialize_and_encrypt()
    {
        $this->encrypter
            ->expects($this->once())
            ->method('encrypt')
            ->with($this->equalTo('O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"baz";s:3:"qux";}'))
        ;

        $this->serializer->serialize((object) ['foo' => 'bar', 'baz' => 'qux']);
    }

    /**
     * @test
     */
    public function it_should_decrypt_and_deserialize()
    {
        $this->encrypter
            ->expects($this->once())
            ->method('decrypt')
            ->with($this->equalTo('encrypted'))
            ->willReturn('O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"baz";s:3:"qux";}')
        ;

        $object = $this->serializer->deserialize('encrypted', 'stdClass');

        $this->assertEquals((object) ['foo' => 'bar', 'baz' => 'qux'], $object);
    }
}
