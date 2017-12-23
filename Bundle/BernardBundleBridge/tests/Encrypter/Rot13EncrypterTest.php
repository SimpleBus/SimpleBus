<?php

namespace SimpleBus\BernardBundleBridge\Tests\Encrypter;

use SimpleBus\BernardBundleBridge\Encrypter\Rot13Encrypter;

/**
 * @group BernardBundleBridge
 */
class Rot13EncrypterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     *
     * @dataProvider getData
     *
     * @param string $string
     * @param string $encrypted
     */
    public function it_encrypts_a_string($string, $encrypted)
    {
        $this->assertEquals($encrypted, (new Rot13Encrypter())->encrypt($string));
    }

    /**
     * @test
     *
     * @dataProvider getData
     *
     * @param string $string
     * @param string $encrypted
     */
    public function it_decrypts_a_string($string, $encrypted)
    {
        $this->assertEquals($string, (new Rot13Encrypter())->decrypt($encrypted));
    }

    public function getData()
    {
        return [
            ['{"foo": "bar"}', '{"sbb": "one"}'],
        ];
    }
}
