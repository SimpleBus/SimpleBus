<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

class AlwaysFailingCommandHandler
{
    public function handle()
    {
        throw new \Exception('I always fail');
    }
}
