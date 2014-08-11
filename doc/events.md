# Events

When a request is sent, some events are triggered through the
[Symfony2 event dispatcher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html) and so, you
can hook into them pretty easily.

## Available events

All available events are described by the constants wrapped in the `Ivory\HttpAdapter\Event\Events` class.

### Pre send

The `Ivory\HttpAdapter\Event\Events::PRE_SEND` describes the event trigger just before a request is sent. It is
represented by the `Ivory\HttpAdapter\Event\PreSendEvent` and wraps the internal request. To get it, you can use:

``` php
$request = $preSendEvent->getRequest();
```

### Post send

The `Ivory\HttpAdapter\Event\Events::POST_SEND` describes the event trigger just after the request is sent. It is
described by the `Ivory\HttpAdapter\Event\PostSendEvent` and wraps the internal request and the response. To get them,
you can use:

``` php
$request = $postSendEvent->getRequest();
$response = $postSendEvent->getResponse();
```

### Exception

The `Ivory\HttpAdapter\Event\Events::EXCEPTION` describes the event trigger if an error occurred. It is represented by
the `Ivory\HttpAdapter\Event\ExceptionEvent` and wraps the internal request and the exception. To get them, you can
use:

``` php
$request = $exceptionEvent->getRequest();
$exception = $exceptionEvent->getException();
```

## Available subscribers

The library provides some useful built-in subscribers you can directly use. Obviously, you can define your own and
propose to add them in the core.

### Logger

The logger subscriber is defined by the `Ivory\HttpAdapter\Event\Subscriber\LoggerSubscriber` and allows you to log all
requests sent/errorred through a PSR logger. As Monolog follows the [PSR-3 Standard](http://www.php-fig.org/psr/psr-3/),
here an example using it and its stream handler but you can use any PSR compliant logger:

``` php
use Ivory\HttpAdapter\Event\Subscriber\LoggerSubscriber;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$monolog = new Logger('name');
$monolog->pushHandler(new StreamHandler('path/to/your.log'));

$loggerSubscriber = new LoggerSubscriber($monolog);

$httpAdapter->getEventDispatcher()->addSubscriber($loggerSubscriber);
```

You can also change the logger at runtime:

``` php
$logger = $loggerSubscriber->getLogger();
$loggerSubscriber->setLogger($logger);
```

### Basic authentication

The basic authentication subscriber is defined by the `Ivory\HttpAdapter\Event\Subscriber\BasicAuthSubscriber` and
allows you to do an HTTP basic authentication. To use it:

``` php
use Ivory\HttpAdapter\Event\Subscriber\BasicAuthSubscriber;

$basicAuthSubscriber = new BasicAuthSubscriber('username', 'password');

$httpAdapter->getEventDispatcher()->addSubscriber($basicAuthSubscriber);
```

Additionally, the basic authentication subscriber accepts a third argument known as matcher. A matcher is responsible
to check if the request should be authenticated according to your rules. It can be either:

 - `null`: all requests are authenticated (default).
 - `string`: only requests with the url matching the string (regex pattern) are authenticated.
 - `callable`: only requests matching your callable are authenticated (the callable receives the event request as
   argument and should return true/false).

Finally, all constructor arguments can be updated at runtime:

``` php
$username = $basicAuthSubscriber->getUsername();
$basicAuthSubscriber->setUsername($username);

$password = $basicAuthSubscriber->getPassword();
$basicAuthSubscriber->setPassword($password);

$hasMatcher = $basicAuthSubscriber->hasMatcher();
$matcher = $basicAuthSubscriber->getMatcher();
$basicAuthSubscriber->setMatcher($matcher);
```
