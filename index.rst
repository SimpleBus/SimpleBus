Welcome to SimpleBus' documentation!
====================================

Simplebus is a organization that helps you to use CQRS and event sourcing in your application.
Get started by reading more about these concepts LINK or by digging in to common use cases LINK.


Features and limitations
========================

Why we do not have queries. Why we chose not to return thins from command handlers.

Package design
==============

Why so many packages. Refer to Matthias Noback's Principle of package design.

.. toctree::
    :maxdepth: 1
    :caption: Introduction

    Introduction/getting-started
    Introduction/organization-overview
    Introduction/cqrs-and-event-sourcing
    Introduction/asynchronous-example

.. toctree::
    :maxdepth: 1
    :glob:
    :caption: Components

    Components/*

.. toctree::
    :maxdepth: 1
    :glob:
    :caption: Bundles

    Bundles/*

.. toctree::
    :maxdepth: 1
    :glob:
    :caption: TODO

    MessageBus/*
    SymfonyBridge/*
