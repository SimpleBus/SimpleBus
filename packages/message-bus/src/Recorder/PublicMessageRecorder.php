<?php

namespace SimpleBus\Message\Recorder;

final class PublicMessageRecorder implements RecordsMessages
{
    use PrivateMessageRecorderCapabilities { record as public; }
}
