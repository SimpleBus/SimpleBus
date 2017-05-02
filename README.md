# SimpleBus/DoctrineDBALBridge

[![Build Status](https://travis-ci.org/SimpleBus/DoctrineDBALBridge.svg?branch=master)](https://travis-ci.org/SimpleBus/DoctrineDBALBridge)
[![codecov](https://codecov.io/gh/SimpleBus/DoctrineDBALBridge/branch/master/graph/badge.svg)](https://codecov.io/gh/SimpleBus/DoctrineDBALBridge)

By [Jasper N. Brouwer](https://github.com/jaspernbrouwer)

This package provides a command bus middleware that can be used to integrate [SimpleBus/MessageBus](https://github.com/SimpleBus/MessageBus) with [Doctrine DBAL](https://github.com/doctrine/dbal).

It provides an easy way to wrap command handling in a database transaction.
