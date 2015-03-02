<?php

namespace SimpleBus\Asynchronous\Message\Publisher;

use SimpleBus\Message\Message;

interface Publisher
{
    /**
     * Publish a message to be handled asynchronously
     *
     * @param Message $message
     * @return void
     */
    public function publish($serializedMessage);
}
