<?php

namespace SimpleBus\Asynchronous\Publisher;

interface Publisher
{
    /**
     * Publish a message to be handled asynchronously.
     */
    public function publish(object $message): void;
}
