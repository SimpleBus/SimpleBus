<?php

namespace SimpleBus\BernardBundleBridge\Encrypter;

class Rot13Encrypter implements Encrypter
{
    public function encrypt($string)
    {
        return str_rot13($string);
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function decrypt($string)
    {
        return str_rot13($string);
    }
}
