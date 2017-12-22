<?php

namespace SimpleBus\BernardBundleBridge\Encrypter;

interface Encrypter
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function encrypt($string);

    /**
     * @param $string
     *
     * @return string
     */
    public function decrypt($string);
}
