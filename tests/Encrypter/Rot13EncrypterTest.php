<?php

namespace SimpleBus\BernardBundleBridge\tests\Encrypter;

use SimpleBus\BernardBundleBridge\Encrypter\Rot13Encrypter;

class Rot13EncrypterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getData
     *
     * @param string $string
     * @param string $encrypted
     */
    public function testEncrypt($string, $encrypted)
    {
        $this->assertEquals($encrypted, (new Rot13Encrypter())->encrypt($string));
    }

    /**
     * @dataProvider getData
     *
     * @param string $string
     * @param string $encrypted
     */
    public function testDecrypt($string, $encrypted)
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
