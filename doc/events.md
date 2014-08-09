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

For now, there is no subscribers, but it will be available soon.
