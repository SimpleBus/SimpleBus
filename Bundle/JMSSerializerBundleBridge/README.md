# SimpleBus/JMSSerializerBundleBridge

[![Build Status](https://travis-ci.org/SimpleBus/JMSSerializerBundleBridge.svg?branch=master)](https://travis-ci.org/SimpleBus/JMSSerializerBundleBridge) [![codecov](https://codecov.io/gh/SimpleBus/JMSSerializerBundleBridge/branch/master/graph/badge.svg)](https://codecov.io/gh/SimpleBus/JMSSerializerBundleBridge)

By [Matthias Noback](http://php-and-symfony.matthiasnoback.nl/)

This library contains a Symfony bundle which configures the `ObjectSerializer` from
[SimpleBus/JMSSerializerBridge](https://github.com/SimpleBus/JMSSerializerBridge) as the default object serializer for
[SimpleBus/AsynchronousBundle](https://github.com/SimpleBus/AsynchronousBundle). You only need to enable the
`SimpleBusJMSSerializerBundleBridgeBundle` in your `AppKernel` to accomplish this.
