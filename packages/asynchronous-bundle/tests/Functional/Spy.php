<?php

declare(strict_types=1);

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

class Spy
{
    /**
     * @var object[]
     */
    public array $handled = [];
}
