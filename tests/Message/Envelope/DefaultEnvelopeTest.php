<?php

namespace Message\Envelope;

use SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelope;

class DefaultEnvelopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function type_should_be_a_string()
    {
        $notAString = 1000000;
        $this->setExpectedException('\InvalidArgumentException');
        new DefaultEnvelope($notAString, 'any string');
    }

    /**
     * @test
     */
    public function serialized_message_should_be_a_string()
    {
        $notAString = 1000000;
        $this->setExpectedException('\InvalidArgumentException');
        new DefaultEnvelope('any string', $notAString);
    }
}
