#!/usr/bin/env bash

git subsplit publish "
    Bridge/DoctrineDBALBridge:git@github.com:SimpleBus/DoctrineDBALBridge.git
    Bridge/DoctrineORMBridge:git@github.com:SimpleBus/DoctrineORMBridge.git
    Bridge/JMSSerializerBridge:git@github.com:SimpleBus/JMSSerializerBridge.git

    Bundle/AsynchronousBundle:git@github.com:SimpleBus/AsynchronousBundle.git
    Bundle/BernardBundleBridge:git@github.com:SimpleBus/BernardBundleBridge.git
    Bundle/JMSSerializerBundleBridge:git@github.com:SimpleBus/JMSSerializerBundleBridge.git
    Bundle/RabbitMQBundleBridge:git@github.com:SimpleBus/RabbitMQBundleBridge.git
    Bundle/SymfonyBridge:git@github.com:SimpleBus/SymfonyBridge.git

    Component/Asynchronous:git@github.com:SimpleBus/Asynchronous.git
    Component/MessageBus:git@github.com:SimpleBus/MessageBus.git
    Component/Serialization:git@github.com:SimpleBus/Serialization.git

    docs:git@github.com:SimpleBus/docs.git
" --update --heads="master gh-pages"
