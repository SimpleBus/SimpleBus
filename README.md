# SimpleBus/JMSSerializerBundleBridge

[![Build Status](https://travis-ci.org/SimpleBus/JMSSerializerBundleBridge.svg?branch=master)](https://travis-ci.org/SimpleBus/JMSSerializerBundleBridge) [![Coverage Status](https://coveralls.io/repos/SimpleBus/JMSSerializerBundleBridge/badge.svg)](https://coveralls.io/r/SimpleBus/JMSSerializerBundleBridge)

By [Matthias Noback](http://php-and-symfony.matthiasnoback.nl/)

This library contains a Symfony bundle which configures the `ObjectSerializer` from
[SimpleBus/JMSSerializerBridge](https://github.com/SimpleBus/JMSSerializerBridge) as the default object serializer for
[SimpleBus/AsynchronousBundle](https://github.com/SimpleBus/AsynchronousBundle). You only need to enable the
`SimpleBusJMSSerializerBundleBridgeBundle` in your `AppKernel` to accomplish this.
