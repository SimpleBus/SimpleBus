#!/usr/bin/env bash

git subsplit publish "
    packages/doctrine-dbal-bridge:git@github.com:SimpleBus/DoctrineDBALBridge.git
    packages/doctrine-orm-bridge:git@github.com:SimpleBus/DoctrineORMBridge.git
    packages/jms-serializer-bridge:git@github.com:SimpleBus/JMSSerializerBridge.git

    packages/asynchronous-bundle:git@github.com:SimpleBus/AsynchronousBundle.git
    packages/jms-serializer-bundle-bridge:git@github.com:SimpleBus/JMSSerializerBundleBridge.git
    packages/rabbitmq-bundle-bridge:git@github.com:SimpleBus/RabbitMQBundleBridge.git
    packages/symfony-bridge:git@github.com:SimpleBus/SymfonyBridge.git

    packages/asynchronous:git@github.com:SimpleBus/Asynchronous.git
    packages/message-bus:git@github.com:SimpleBus/MessageBus.git
    packages/serialization:git@github.com:SimpleBus/Serialization.git

    docs:git@github.com:SimpleBus/docs.git
" --update --heads="master gh-pages"
