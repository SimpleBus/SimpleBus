<?php

namespace SimpleBus\Message\Tests\Recorder\Fixtures;

use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;

final class PrivateMessageRecorderCapabilitiesStub implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    public function publicRecord(object $message): void
    {
        $this->record($message);
    }
}
