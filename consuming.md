# Consuming messages

To consume messages in the queue run the following:

```bash
./app/console bernard:consume <queue-name>
```

Above will start a PHP process (essentially a loop), looking up for the messages in specified queue. To end the process press `CTLR+c`.

PHP is meant to die, hence it is not recommended to rely on endless `bernard:consume` execution. Especially when you deal with Doctrine, filling it's identity map with objects, thus consuming more and more memory. Extra care must be taken to clear _EntityManager_ approprietly, make sure garbage collector is executed by running `gc_collect_cycles()` function etc. Unless you know what you're doing it is expected for `bernard:consume` to exit.

If you don't want to deal with this yourself, you can enable the [LongRunningBundle](https://github.com/LongRunning/LongRunning) to automatically cleanup after a message is consumed.

## Using cron

Below example consumes messages for 5 minutes and exits:

```bash
*/5 * * * * /var/www/symfony/app/console bernard:consume --max-runtime=300 >> /var/log/symfony/cron.log 2>&1
```

In other words, a cron job is run in 5 minutes interval consuming messages during 5 minutes i.e. there is always an active process.

When amount of incoming messages is low, you could do something like this:

```bash
*/3 * * * * /var/www/symfony/app/console bernard:consume --max-messages=90 >> /var/log/symfony/cron.log 2>&1
```

Consume 90 messages once per 3 minutes. You need to make sure your app can process 30 messages per minute. Adjust amount of messages and time to process it accordingly.

## Using supervisor

The best way to keep the process alive is with [supervisor](http://supervisord.org).

Consider below example:

```ini
[program:geo_location]
directory   = /var/www/symfony
user        = symfony
command     = ./app/console bernard:consume --max-runtime=300 geo_location
autorestart = true

[program:mailer_webhook]
directory   = /var/www/symfony
user        = symfony
command     = ./app/console bernard:consume --max-runtime=300 mailer_webhook
autorestart = true

[program:mailer_delivery]
directory    = /var/www/symfony
user         = symfony
command      = ./app/console bernard:consume --max-runtime=300 mailer_delivery
autorestart  = true
numprocs     = 2
process_name = %(program_name)s_%(process_num)01d

[group:bernard]
programs = geo_location,mailer_webhook,mailer_delivery
```

Starting _Bernard_ processes:

```bash
$ sudo supervisorctl start bernard:*
```

Above will spawn 4 `bernard:consume` instances. 1 process for _geo_location_ and _mailer_webhook_ and 2 processes for _mailer_delivery_. The latter queue is processed faster as two workers deal with it.

## Next

Read next about messages [encryption and logging](https://github.com/lakiboy/SimpleBusBernardBundleBridge/blob/master/doc/features.md).
