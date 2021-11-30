<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use Exception;

final class AlwaysFailingCommandHandler
{
    public function handle(): void
    {
        throw new Exception('I always fail');
    }
}
