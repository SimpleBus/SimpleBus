<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use Exception;

class AlwaysFailingCommandHandler
{
    public function handle(): void
    {
        throw new Exception('I always fail');
    }
}
