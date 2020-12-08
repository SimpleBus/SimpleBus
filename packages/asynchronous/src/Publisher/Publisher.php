<?php

namespace SimpleBus\Asynchronous\Publisher;

interface Publisher
{
    /**
     * Publish a message to be handled asynchronously
     *
     * @param object $message
     * @return void
     */
    public function publish($message);
}
