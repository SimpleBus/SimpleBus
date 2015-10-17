<?php

namespace SimpleBus\BernardBundleBridge\Tests\Encrypter;

use SimpleBus\BernardBundleBridge\Encrypter\NelmioEncrypter;

/**
 * @requires extension mcrypt
 */
class NelmioEncrypterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function it_should_reject_invalid_algorithm()
    {
        new NelmioEncrypter('secret', 'invalid_algoritm');
    }

    /**
     * @test
     */
    public function it_should_encrypt_and_decrypt()
    {
        $encrypter = new NelmioEncrypter('secret', 'rijndael-128');

        $value = 'foo';
        $encryptedValue = $encrypter->encrypt($value);
        $decryptedValue = $encrypter->decrypt($encryptedValue);

        $this->assertNotEquals($encryptedValue, $value);
        $this->assertEquals($decryptedValue, $value);
    }

    /**
     * @test
     */
    public function it_should_return_null_on_empty_input()
    {
        $encrypter = new NelmioEncrypter('secret', 'rijndael-128');

        $this->assertNull($encrypter->encrypt(''));
        $this->assertNull($encrypter->decrypt(''));
    }
}
