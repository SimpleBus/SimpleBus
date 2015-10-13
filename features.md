# Features

## Encryption

_SimpleBusBernardBundleBridge_ supports message encryption. This is useful when transfering sensitive data using some 3rd party service or over unencrypted channel.

Minimal configuration to enable encryption is as follows:

```yaml
simple_bus_bernard_bundle_bridge:
    encryption: ~
```

By default _nelmio_ encryper is used, hence _mcrypt_ PHP extensions must be installed. You can also adjust a secret key and encryption algorithm:

```yaml
simple_bus_bernard_bundle_bridge:
    encryption:
        encrypter: nelmio # default
        secret: my_secret # default: %kernel.secret% 
        algorithm: des    # default: rijndael-128
```

Alternative lightweight `rot13` encrypter is supported, not recommended for production use though. Custom encrypter service is supported as well by implementing `SimpleBus\BernardBundleBridge\Encrypter\Encrypter` interface:

```yaml
simple_bus_bernard_bundle_bridge:
    encryption:
        encrypter: my_encrypter
```
