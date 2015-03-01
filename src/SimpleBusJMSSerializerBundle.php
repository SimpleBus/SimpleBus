<?php

namespace SimpleBus\JMSSerializerBundle;

use SimpleBus\JMSSerializerBundle\DependencyInjection\SimpleBusJMSSerializerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusJMSSerializerBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SimpleBusJMSSerializerExtension();
    }
}
