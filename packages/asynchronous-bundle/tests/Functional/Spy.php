<?php

declare(strict_types=1);

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

final class Spy
{
    /**
     * @var object[]
     */
    public array $handled = [];
}
