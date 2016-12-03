Features
========

Encryption
----------

*SimpleBusBernardBundleBridge* supports messages encryption. This is
useful when transfering sensitive data using some 3rd party service or
over unencrypted channel.

Minimal configuration to enable encryption is as follows:

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        encryption: ~

By default *nelmio* encrypter is used. This requires *mcrypt* PHP
extension to be installed. You can also adjust a secret key and
encryption algorithm:

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        encryption:
            encrypter: nelmio # default
            secret: my_secret # default: %kernel.secret% 
            algorithm: des    # default: rijndael-128

Alternative lightweight ``rot13`` encrypter is supported, however not
recommended for production use. Custom encrypter service is available as
well by implementing
``SimpleBus\BernardBundleBridge\Encrypter\Encrypter`` interface:

.. code:: yaml

    simple_bus_bernard_bundle_bridge:
        encryption:
            encrypter: my_encrypter

Logging
-------

You can enable logger listener to debug messages production, consumption
and rejection. Consider below example in development config:

.. code:: yaml

    # config_dev.yml

    monolog:
        channels: [ bernard ]

        handlers:
            ...

            bernard:
                type:     stream
                path:     "%kernel.logs_dir%/bernard.%kernel.environment%.log"
                level:    info
                channels: [ bernard ]

    simple_bus_bernard_bundle_bridge:
        logger: monolog.logger.bernard

Then just tail the logs with:

.. code:: bash

    $ tail -f app/logs/bernard.dev.log

Please, refer to
`BernardBundle <https://github.com/bernardphp/BernardBundle>`__
documention how to implement your own listeners.

Using doctrine driver
---------------------

*Bernard* supports ``doctrine`` adapter, which uses SQL tables to store
messages. If this is the case, then *SimpleBusBernardBundleBridge* turns
SQL logging off for all registered *Doctrine* connections when running
``bernard:consume`` console command. It prevents the consume process to
run ouf of memory.

Next
----

Read the
`cookbook <https://github.com/SimpleBus/SimpleBusBernardBundleBridge/blob/master/doc/cookbook.md>`__
for various recipes how to use *SimpleBusBernardBundleBridge* together
with *Bernard*.
