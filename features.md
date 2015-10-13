# Features

## Encryption

_SimpleBusBernardBundleBridge_ supports messages encryption. This is useful when transfering sensitive data using some 3rd party service or over unencrypted channel.

Minimal configuration to enable encryption is as follows:

```yaml
simple_bus_bernard_bundle_bridge:
    encryption: ~
```

By default _nelmio_ encrypter is used. This requires _mcrypt_ PHP extension to be installed. You can also adjust a secret key and encryption algorithm:

```yaml
simple_bus_bernard_bundle_bridge:
    encryption:
        encrypter: nelmio # default
        secret: my_secret # default: %kernel.secret% 
        algorithm: des    # default: rijndael-128
```

Alternative lightweight `rot13` encrypter is supported, however not recommended for production use. Custom encrypter service is available as well by implementing `SimpleBus\BernardBundleBridge\Encrypter\Encrypter` interface:

```yaml
simple_bus_bernard_bundle_bridge:
    encryption:
        encrypter: my_encrypter
```

## Logging

You can enable logger listener to debug messages production, consumption and rejection. Consider below example in development config:

```yaml
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
```

Then just tail the logs with:

```bash
$ tail -f app/logs/bernard.dev.log
```

Please, refer to [BernardBundle](https://github.com/bernardphp/BernardBundle) documention how to implement your own listeners.

## Using doctrine driver

_Bernard_ supports `doctrine` adapter, which uses SQL tables to store messages. If this is the case, then _SimpleBusBernardBundleBridge_ turns SQL logging off for all registered _Doctrine_ connnections when running `bernard:consume` console command. It prevents the consume process to run ouf of memory.

## Next

Read the [cookbook](https://github.com/lakiboy/SimpleBusBernardBundleBridge/blob/master/doc/cookbook.md) for various recipes how to use _SimpleBusBernardBundleBridge_ together with _Bernard_.
