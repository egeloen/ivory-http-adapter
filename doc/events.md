# Events

If you use the `Ivory\HttpAdapter\EventDispatcherHttpAdapter`, then, when a request is sent, some events are triggered
through the [Symfony2 event dispatcher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)
and so, you can hook into them pretty easily.

## Available events

All available events are described by the constants wrapped in the `Ivory\HttpAdapter\Event\Events` class. There are
two types of events:

 * Serial events
 * Parallel events

### Request created

The `Ivory\HttpAdapter\Event\Events::REQUEST_CREATED` describes the event trigger just before a request is sent serially. It
is represented by the `Ivory\HttpAdapter\Event\RequestCreatedEvent` and wraps the http adapter and the internal request.
To get/set them, you can use:

``` php
use Ivory\HttpAdapter\Event\RequestCreatedEvent;

$requestCreatedEvent = new RequestCreatedEvent($httpAdapter, $request);

$httpAdapter = $requestCreatedEvent->getHttpAdapter();

$request = $requestCreatedEvent->getRequest();
$requestCreatedEvent->setRequest($request);
```

### Request sent

The `Ivory\HttpAdapter\Event\Events::REQUEST_SENT` describes the event trigger just after the request is sent serially. It
is described by the `Ivory\HttpAdapter\Event\RequestSentEvent` and wraps the http adapter, the internal request and the
response. To get/set them, you can use:

``` php
use Ivory\HttpAdapter\Event\RequestSentEvent;

$requestSentEvent = new RequestSentEvent($httpAdapter, $request, $response);

$httpAdapter = $requestSentEvent->getHttpAdapter();

$request = $requestSentEvent->getRequest();
$requestSentEvent->setRequest($request);

$response = $requestSentEvent->getResponse();
$requestSentEvent->setResponse($response);
```

### Request errored

The `Ivory\HttpAdapter\Event\Events::REQUEST_ERRORED` describes the event trigger if an error occurred in a serial request
context. It is represented by the `Ivory\HttpAdapter\Event\RequestErroredEvent` and wraps the http adapter and the
exception. To get/set them, you can use:

``` php
use Ivory\HttpAdapter\Event\RequestErroredEvent;

$requestErroredEvent = new RequestErroredEvent($httpAdapter, $request, $exception);

$httpAdapter = $requestErroredEvent->getHttpAdapter();

$exception = $requestErroredEvent->getException();
$requestErroredEvent->setException($exception);

$request = $exception->getRequest();
$response = $exception->getResponse();
```

Additionally, this event allows you to manage a response. That means if you want to by-pass the exception, just set the
response on the event and it will be returned by the http adapter instead of throwing the exception. The following API
is available:

```php
$hasResponse = $requestErroredEvent->hasResponse();
$response = $requestErroredEvent->getResponse();
$requestErroredEvent->setResponse($response);
```

### Multi request created

The `Ivory\HttpAdapter\Event\Events::MULTI_REQUEST_CREATED` describes the event trigger just before multiple requests are sent
in parallel. It is represented by the `Ivory\HttpAdapter\Event\MultiRequestCreatedEvent` and wraps the http adapter and
the internal requests. To get/set them, you can use:

``` php
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;

$multiRequestCreatedEvent = new MultiRequestCreatedEvent($httpAdapter, $requests);

$httpAdapter = $multiRequestCreatedEvent->getHttpAdapter();

$requests = $multiRequestCreatedEvent->getRequests();
$multiRequestCreatedEvent->setRequests($requests);
```

### Multi request sent

The `Ivory\HttpAdapter\Event\Events::MULTI_REQUEST_SENT` describes the event trigger just after multiple requests are sent
**fully successfully** in parallel. To be even more precise, if only one request is errored, this event is not
triggered. It is described by the `Ivory\HttpAdapter\Event\MultiRequestSentEvent` and wraps the http adapter and the
responses. To get/set them, you can use:

``` php
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;

$multiRequestSentEvent = new MultiRequestSentEvent($httpAdapter, $responses);

$httpAdapter = $multiRequestSentEvent->getHttpAdapter();

$responses = $multiRequestSentEvent->getResponses();
$multiRequestSentEvent->setResponses($responses);
```

All responses wrap the linked request as parameter:

``` php
foreach ($responses as $response) {
    $request = $response->getParameter('request');
}
```

### Multi request errored

The `Ivory\HttpAdapter\Event\Events::MULTI_REQUEST_ERRORED` describes the event trigger if an error occurred in a parallel
requests context. It is represented by the `Ivory\HttpAdapter\Event\MultiRequestErroredEvent` and wraps the http adapter
exceptions. To get/set them, you can use:

``` php
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;

$multiRequestErroredEvent = new MultiRequestErroredEvent($httpAdapter, $multiException);

$httpAdapter = $multiRequestErroredEvent->getHttpAdapter();

$exceptions = $multiRequestErroredEvent->getExceptions();
$multiRequestErroredEvent->setExceptions($exceptions);
```

The exception is an instance of `Ivory\HttpAdapter\HttpAdapterException` which wraps the request and the responses if
available. To get/set them, you can use:

``` php
foreach ($mutliRequestErroredEvent->getExceptions() as $exception) {
    $request = $exception->getRequest();

    if ($exception->hasResponses()) {
        $responses = $exception->getResponse();
    }
}
```

## Available subscribers

The library provides some useful built-in subscribers you can directly use. Obviously, you can define your own and
propose to add them in the core. The built-in ones are:

 * [Basic authentication](/doc/events.md#basic-authentication)
 * [Cookie](/doc/events.md#cookie)
 * [History](/doc/events.md#history)
 * [Logger](/doc/events.md#logger)
 * [Redirect](/doc/events.md#redirect)
 * [Retry](/doc/events.md#retry)
 * [Status](/doc/events.md#status-code)
 * [Stopwatch](/doc/events.md#stopwatch)

Some other event subscribers are available as dedicated packages:

 * [Tape recorder](https://github.com/kreait/tape-recorder-subscriber)

### Basic authentication

The basic authentication subscriber is defined by the `Ivory\HttpAdapter\Event\Subscriber\BasicAuthSubscriber` and
allows you to do an HTTP basic authentication. To use it:

``` php
use Ivory\HttpAdapter\Event\BasicAuth\BasicAuth;
use Ivory\HttpAdapter\Event\Subscriber\BasicAuthSubscriber;

$basicAuthSubscriber = new BasicAuthSubscriber(new BasicAuth('username', 'password'));
```

Internally, the basic auth subscriber uses the `Ivory\HttpAdapter\Event\BasicAuth\BasicAuth` in order to do the
authentication. Since the username and passord is mandatory, the basic auth is too. It takes respectively as first and
second argument the username and the password. The basic authentication accepts a third argument known as matcher. A
matcher is responsible to check if the request should be authenticated according to your rules. It can be either:

 - `null`: all requests are authenticated (default).
 - `string`: only requests with the url matching the string (regex pattern) are authenticated.
 - `callable`: only requests matching your callable are authenticated (the callable receives the event request as
   argument and should return true/false).

Finally, all constructor arguments can be updated at runtime:

``` php
$username = $basicAuth->getUsername();
$basicAuth->setUsername($username);

$password = $basicAuth->getPassword();
$basicAuth->setPassword($password);

$hasMatcher = $basicAuth->hasMatcher();
$matcher = $basicAuth->getMatcher();
$basicAuth->setMatcher($matcher);
```

### Cookie

The cookie subscriber is defined by the `Ivory\HttpAdapter\Event\Subscriber\CookieSubscriber` and allow you to manage
cookies through a jar. To use it:

``` php
use Ivory\HttpAdapter\Event\Subscriber\CookieSubscriber;

$cookieSubscriber = new CookieSubscriber();
```

By default, a cookie jar is created by the subscriber but you can specify it in its constructor:

``` php
use Ivory\HttpAdapter\Event\Cookie\Jar\CookieJar;
use Ivory\HttpAdapter\Event\Subscriber\CookieSubscriber;

$cookieJar = new CookieJar();
$cookieSubscriber = new CookieSubscriber($cookieJar);
```

Finally, the cookie jar can be access or change at runtime with:

``` php
$cookieJar = $cookieSubscriber->getCookieJar();
$cookieSubscriber->setCookieJar($cookieJar);
```

#### Cookie Jar

A cookie jar is described by the `Ivory\HttpAdapter\Event\Cookie\Jar\CookieJarInterface` and its default implementation
is `Ivory\HttpAdapter\Event\Cookie\Jar\CookieJar`. As there is an interface, you can define your own implementation.

First, a cookie jar stores/manages the cookies. You can use the following API:

``` php
$hasCookies = $cookieJar->hasCookies();
$cookies = $cookieJar->getCookies();
$cookieJar->setCookies($cookies);
$cookieJar->addCookies($cookies);
$cookieJar->removeCookies($cookies);

$hasCookie = $cookieJar->hasCookie($cookie);
$cookieJar->addCookie($cookie);
$cookieJar->removeCookie($cookie);

// Clean expired cookies
$cookieJar->clean();

// Clear cookies
$cookieJar->clear($domain, $path, $name);
```

Second, the cookie jar creates the cookies through the `Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface`
and its default implementation is `Ivory\HttpAdapter\Event\Cookie\CookieFactory`. It can be configured via the
constructor or getter/setter:

``` php
use Ivory\HttpAdapter\Event\Cookie\CookieFactory;
use Ivory\HttpAdapter\Event\Cookie\Jar\CookieJar;

$cookieJar = new CookieJar(new CookieFactory());

$cookieFactory = $cookieJar->getCookieFactory();
$cookieJar->setCookieFactory($cookieFactory);
```

Third, it is responsible to populate cookies in a request and extract them from a response with the following
API:

``` php
$cookieJar->populate($request);
$cookieJar->extract($request, $response);
```

Fourth, the cookie jar implements the `Countable` interface, so if you wants to know how many cookies are in it, you
can use:

``` php
$count = count($cookieJar);
```

Fifth, the cookies implements the `IteratorAggregator` interface, so, you can directly access cookies with the
following code:

``` php
foreach ($cookieJar as $cookie) {
    // Do what you want with the cookie
}

// or

$cookies = iterator_to_array($cookieJar);
```

#### Persistent cookie jar

The persistent cookie jar is described by the `Ivory\HttpAdapter\Event\Cookie\Jar\PersistentCookieJarInterface` and its
default implementation is the `Ivory\HttpAdapter\Event\Cookie\Jar\AbstractPersistentCookieJar` (not directly usable).
Basically, it allows you to load/save cookies from/to somewhere. All persistent cookie jars share the following API:

``` php
// Loads the cookie jar from the underlying resource
$cookieJar->load();

// Saves the cookie jar on the underlying resource
$cookieJar->save();
```

Additionally, the persistent cookie jar will automatically try to load it when it is instantiated and will save it when
it is destroyed.

##### File cookie jar

The file cookie jar is a persistent cookie jar which stores/retrieves cookies from a file. To use it:

``` php
use Ivory\HttpAdapter\Event\Cookie\Jar\FileCookieJar;

$cookieJar = new FileCookieJar('path/to/the/file');
```

##### Session cookie jar

The session cookie jar is a persistent cookie jar which stores/retrieves cookies from the session. To use it:

``` php
use Ivory\HttpAdapter\Event\Cookie\Jar\SessionCookieJar;

$cookieJar = new SessionCookieJar('session_key');
```

#### Cookie factory

As already explained, the cookie factory is defined by the `Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface`
and its default implementation is `Ivory\HttpAdapter\Event\Cookie\CookieFactory`. It is responsible to instantiate a
cookie. For that, you can directly instantiate it or parse an header:

``` php
$cookie = $cookieFactory->create($name, $value, $attributes, $createdAt);
// or
$cookie = $cookieFactory->parse($header);
```

#### Cookie

A cookie is describes by the `Ivory\HttpAdapter\Event\Cookie\CookieInterface` and its default implemenation is
`Ivory\HttpAdapter\Event\Cookie\Cookie`. As there is an interface, you can define your own implementation.

A cookie stores all the informations related to it. You can get/set them with:

``` php
use Ivory\HttpAdapter\Event\Cookie\Cookie;

$cookie = new Cookie($name, $value, $attributes, $createdAt);

$hasName = $cookie->hasName();
$name = $cookie->getName();
$cookie->setName($name);

$hasValue = $cookie->hasValue();
$value = $cookie->getValue();
$cookie->setValue($value);

$hasAttributes = $cookie->hasAttributes();
$attributes = $cookie->getAttributes();
$cookie->setAttributes($attributes);
$cookie->addAttributes($attributes);
$cookie->removeAttributes($names);
$cookie->clearAttributes();

$hasAttribute = $cookie->hasAttributes($name);
$value = $cookie->getAttribute($name);
$cookie->setAttribute($name, $value);
$cookie->removeAttribute($name);

$createdAt = $cookie->getCreatedAt();
$cookie->setCreatedAt($createdAt);
```

All attribute names are described by the `Ivory\HttpAdapter\Event\Cookie\Cookie::ATTR_*` constants.

To check/get the expiration date according to the `Expires` pr `Max-Age` attribute, you can use the following method:

``` php
$expired = $cookie->isExpired();
$expires = $cookie->getExpires();
```

Additionally, you can check if the request matches a request or just matches a part of the request:

``` php
$match = $cookie->match($request);
$match = $cookie->matchDomain($domain);
$match = $cookie->matchPath($path);
$match = $cookie->matchScheme($scheme);
```

Furthermore, you can compare two cookies:

``` php
$compare = $cookie->compare($otherCookie);
```

You can also convert the cookie into an array or a string:

``` php
$array = $cookie->toArray();
$string = (string) $cookie;
```

### History

The history subscriber is defined by the `Ivory\HttpAdapter\Event\Subscriber\HistorySubscriber` and allow you to
maintain an history of all requests/responses sent through a journal. To use it:

``` php
use Ivory\HttpAdapter\Event\Subscriber\HistorySubscriber;

$historySubscriber = new HistorySubscriber();
```

By default, a journal is created by the subscriber but you can specify it in its constructor:

``` php
use Ivory\HttpAdapter\Event\Subscriber\History\Journal;
use Ivory\HttpAdapter\Event\Subscriber\HistorySubscriber;

$journal = new Journal();
$historySubscriber = new HistorySubscriber($journal);
```

Finally, the journal can be access or change at runtime with:

``` php
$journal = $historySubscriber->getJournal();
$historySubscriber->setJournal($journal);
```

#### Journal

A journal is described by the `Ivory\HttpAdapter\Event\Subscriber\History\JournalInterface` and its default
implementation is `Ivory\HttpAdapter\Event\Subscriber\History\Journal`. As there is an interface, you can define your
own implementation.

So, a journal wraps a limit which represents the maximum number of allowed entries in the journal (default: 10) but
can be configured via getter/setter:

``` php
$limit = $journal->getLimit();
$journal->setLimit($limit);
```

Second, the journal wraps all entries of the history according to the limit (the last entries are kept in the journal
and the last ones are dropped when a new one is added). The following API allows you to check/get/set/clear the entries:

``` php
$hasEntries = $journal->hasEntries();
$entries = $journal->getEntries();
$journal->setEntries($entries);
$journal->addEntries($entries);
$journal->removeEntries($entries);

$hasEntry = $journal->hasEntry($entry);
$journal->addEntry($entry);
$journal->removeEntry($entry);

$journal->clear();
```

Third, a journal creates the entries through the `Ivory\HttpAdapter\Event\History\JournalEntryFactoryInterface` and
its default implementation is `Ivory\HttpAdapter\Event\History\JournalEntryFactory`. It can be configured via the
constructor or getter/setter:

``` php
use Ivory\HttpAdapter\Event\Subscriber\History\Journal;
use Ivory\HttpAdapter\Event\Subscriber\History\JournalEntryFactory;

$journal = new Journal(new JournalEntryFactory());

$entryJournalFactory = $journal->getJournalEntryFactory();
$journal->setJournalEntryFactory($entryJournalFactory);
```

Fourth, the journal implements the `Countable` interface, so if you wants to know how many entries are in the journal,
you can use:

``` php
$count = count($journal);
```

Fifth, the journal implements the `IteratorAggregator` interface, so, you can directly access entries with the
following code but the entries are ordered from the most recent to the most old:

``` php
foreach ($journal as $entry) {
    // Do what you want with the entry
}

// or

$entries = iterator_to_array($journal);
```

#### Journal entry

A journal entry is described by the `Ivory\HttpAdapter\Event\Subscriber\History\JournalEntryInterface` and its default
implementation is `Ivory\HttpAdapter\Event\Subscriber\History\JournalEntry`. As there is an interface, you can define
your own implementation through the factory.

It wraps the request and the response. To get/set them, you can use the following API:

``` php
use Ivory\HttpAdapter\Event\Subscriber\History\JournalEntry;

$entry = new JournalEntry($request, $response);

$request = $entry->getRequest();
$entry->setRequest($request);

$response = $entry->getResponse();
$entry->setResponse($response);
```

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
```

You can also change the logger at runtime:

``` php
$logger = $loggerSubscriber->getLogger();
$loggerSubscriber->setLogger($logger);
```

Internally, the logger subscriber uses the `Ivory\HttpAdapter\Event\Formatter\Formatter` in order to format the log
context and and the `Ivory\HttpAdapter\Timer\Timer` in order to time the request. By default, if you don't provide
them, it will be created automatically. If you want to use your own, you can pass them via constructor or getter/setter:

``` php
use Ivory\HttpAdapter\Event\Formatter\Formatter;
use Ivory\HttpAdapter\Event\Subscriber\LoggerSubscriber;
use Ivory\HttpAdapter\Event\Timer\Timer;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$monolog = new Logger('name');
$monolog->pushHandler(new StreamHandler('path/to/your.log'));

$loggerSubscriber = new LoggerSubscriber($monolog, new Formatter(), new Timer());

$formatter = $loggerSubscriber->getFormatter();
$loggerSubscriber->setFormatter($formatter);

$timer = $loggerSubscriber->getTimer();
$loggerSubscriber->setTimer($timer);
```

### Redirect

The redirect subscriber is defined by the `Ivory\HttpAdapter\Event\Subscriber\RedirectSubscriber` and allow you to
follow redirects. Basically, by default, all http adapters don't follow the redirect and will give you the 3xx
response. Then, if you want to follow redirect, just register the redirect subscriber:

``` php
use Ivory\HttpAdapter\Event\Subscriber\RedirectSubscriber;

$redirectSubscriber = new RedirectSubscriber();
```

When you use the redirect subscriber, some parameters are available on the response:

 - `effective_url`: The final url of the redirection.
 - `redirect_count`: The number of redirects which have been followed.

Internally, the redirect subscriber uses the `Ivory\HttpAdapter\Event\Redirect\Redirect` in order to do a redirect.
By default, if you don't provide it, it will be created automatically. If you want to use your own, you can pass it via
constructor or getter/setter:

``` php
use Ivory\HttpAdapter\Redirect\Redirect;
use Ivory\HttpAdapter\Event\Subscriber\RedirectSubscriber;

$redirectSubscriber = new RedirectSubscriber(new Redirect());

$redirect = $redirectSubscriber->getRedirect();
$redirectSubscriber->setRedirect($redirect);
```

The redirect is also configurable and allow you to modify its behavior.

First, by default, the redirect allows you to follow 5 redirects. If you want to increase or decrease it, you can
specify it as first constructor argument or via getter/setter:

``` php
use Ivory\HttpAdapter\Event\Redirect\Redirect;

$redirect = new Redirect(10);

$max = $redirect->getMax();
$redirect->setMax($max);
```

Second, by default, the redirect does not follow strictly the RFC and will prefer to do what most browser does (convert
to a GET request). If you want to follow strictly the RFC, you can specify it as second constructor argument or via
getter/setter:

``` php
use Ivory\HttpAdapter\Event\Redirect\Redirect;

$redirect = new Redirect(5, true);

$strict = $redirect->isStrict();
$redirect->setStrict($strict);
```

Third, by default, the redirect will throw an exception when the maximum number of redirects is exceeded. If you want
to just stop the redirection and return the redirect response reached, you can specify it as third constructor argument
or via getter/setter:

``` php
use Ivory\HttpAdapter\Event\Redirect\Redirect;

$redirect = new Redirect(5, false, false);

$throwException = $redirect->getThrowException();
$redirect->setThrowException($throwException);
```

### Retry

The retry subscriber is defined by the `Ivory\HttpAdapter\Event\Subscriber\RetrySubscriber` and allow you to retry an
errored request (more precisely when an exception is thrown). To use it:

``` php
use Ivory\HttpAdapter\Event\Subscriber\RetrySubscriber;

$retrySubscriber = new RetrySubscriber();
```

When you use the retry subscriber, some parameters are available on the request:

 - `retry_count`: The number of retries which have been done.

Internally, the retry subscriber uses the `Ivory\HttpAdapter\Event\Retry\Retry` in order to do a retry. By default, if
you don't provide it, it will be created automatically. If you want to use your own, you can pass it via constructor or
getter/setter:

``` php
use Ivory\HttpAdapter\Event\Subscriber\RetrySubscriber;
use Ivory\HttpAdapter\Event\Retry\Retry;

$retrySubscriber = new RetrySubscriber(new Retry());

$retry = $retrySubscriber->getRetry();
$retrySubscriber->setRetry($retry);
```

The retry is also configurable and allows you to modify its behavior.

By default, the retry uses a limited retry strategy of 3 retries combined to an exponential delayed one. Basically,
that means it will retry 3 times maximum and wait more and more between each retry (2 ^ retry_count). This strategy can
be configured through the constructor or via getter/setter:

``` php
use Ivory\HttpAdapter\Event\Retry\Strategy\ExponentialDelayedRetryStrategy;
use Ivory\HttpAdapter\Event\Retry\Strategy\LimitedRetryStrategy;

$retry = new Retry(new LimitedRetryStrategy(3, new ExponentialDelayedRetryStrategy()));

$strategy = $retry->getStrategy();
$retry->setStrategy($strategy);
```

If you want to know more about the retry strategies, the next sections are for you.

#### Constant Delayed Strategy

The constant delayed retry strategy is defined by the `Ivory\HttpAdapter\Event\Retry\Strategy\ConstantDelayedRetryStrategy`
and allow you to retry a request following the exact same delay between each retry (by default: 5 seconds).

``` php
use Ivory\HttpAdapter\Event\Retry\Strategy\ConstantDelayedRetryStrategy;

$retryStrategy = new ConstantDelayedRetryStrategy();
```

The delay can be configured through the constructor or via getter/setter:

``` php
use Ivory\HttpAdapter\Event\Retry\Strategy\ConstantDelayedRetryStrategy;

$retryStrategy = new ConstantDelayedRetryStrategy(2);

$delay = $retryStrategy->getDelay();
$retryStrategy->setDelay(2);
```

#### Linear Delayed Strategy

The linear delayed retry strategy is defined by the `Ivory\HttpAdapter\Event\Retry\Strategy\LinearDelayedRetryStrategy`
and allow you to retry a request following a linear delay between each retry (delay = configured delay * retry count).
By default, the configured delay is 5 seconds.

``` php
use Ivory\HttpAdapter\Event\Retry\Strategy\LinearDelayedRetryStrategy;

$retryStrategy = new LinearDelayedRetryStrategy();
```

The delay can be configured through the constructor or via getter/setter:

``` php
use Ivory\HttpAdapter\Event\Retry\Strategy\LinearDelayedRetryStrategy;

$retryStrategy = new LinearDelayedRetryStrategy(2);

$delay = $retryStrategy->getDelay();
$retryStrategy->setDelay(2);
```

#### Exponential Delayed Strategy

The exponential retry strategy is defined by the `Ivory\HttpAdapter\Event\Retry\Strategy\ExponentialRetryStrategy` and
allow you to retry a request following an exponential delay between each retry (delay = 2 ^ redirect count).

``` php
use Ivory\HttpAdapter\Event\Retry\Strategy\ExponentialDelayedRetryStrategy;

$retryStrategy = new ExponentialDelayedRetryStrategy();
```

#### Limited Strategy

The limited retry strategy is defined by the `Ivory\HttpAdapter\Event\Retry\Strategy\LimitedRetryStrategy` and allow
you to limit the number of retry (by default: 3).

``` php
use Ivory\HttpAdapter\Event\Retry\Strategy\LimitedRetryStrategy;

$retryStrategy = new LimitedRetryStrategy();
```

The limit can be configured through the constructor or via getter/setter:

``` php
use Ivory\HttpAdapter\Event\Retry\Strategy\LimitedRetryStrategy;

$retryStrategy = new LimitedRetryStrategy(5);

$limit = $retryStrategy->getLimit();
$retryStrategy->setLimit(5);
```

#### Callback Strategy

The callback retry strategy is defined by the `Ivory\HttpAdapter\Event\Retry\Strategy\CallbackRetryStrategy` and allow
you to limit the number of retry or specify the delay between each retry through two callbacks. They can be configured
through the constructor or via getter/setter:

``` php
use Ivory\HttpAdapter\Event\Retry\Strategy\CallbackRetryStrategy;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

$retryStrategy = new CallbackRetryStrategy(
    // Verify callback
    function (InternalRequestInterface $request, HttpAdapterException $exception) {
        // Return TRUE if you want to retry the request otherwise FALSE.
    },
    // Delay callback
    function (InternalRequestInterface $request, HttpAdapterException $exception) {
        // Return the delay to wait before retrying the request.
    }
);

$hasVerifyCallback = $retryStrategy->hasVerifyCallback();
$verifyCallback = $retryStrategy->getVerifyCallback();
$retryStrategy->setVerifyCallback($verifyCallback);

$hasDelayCallback = $retryStrategy->hasDelayCallback();
$delayCallback = $retryStrategy->getDelayCallback();
$retryStrategy->setDelayCallback($delayCallback);
```

The two callbacks are not mandatory. If you don't provide the verify callback, it will consider it should retry the
request and if you don't provide the delay callback, it will consider it should not wait before retrying the request.

#### Custom Strategy

All retry strategies implement the `Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface`. Then, if you want
to define your own strategy, you will need to implement this interface. Here, an example which rely on information
wraps in the header:

``` php
namespace My\Own\Namespace;

use Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

class MyOwnRetryStrategy implements RetryStrategyInterface
{
    public function verify(InternalRequestInterface $request)
    {
        return $request->getHeader('X-Can-Retry') === 'true';
    }

    public function delay(InternalRequestInterface $request)
    {
        return (float) $request->getHeader('X-Retry-Delay');
    }
}
```

#### Chain of Responsibility

All retry strategies implement a chain of responsibility. That means you can combine strategies together in order to
archive more complex behavior. For example, by default, the retry subscriber combines the limited retry strategy with
the exponential delayed one.

To do that, all retry strategies implement the `Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyChainInterface` and
extend the `Ivory\HttpAdapter\Event\Retry\Strategy\AbstractRetryStrategyChain` where the default chain of responsibility
implementation is done. Then, a chained retry strategy wraps a next strategy which is involved when it decides if it
should retry the request and for calculating the delay to wait if it should retry.

The behavior of the chain of responsibility is the following:

 - It retries a request if all retry strategies in the chain of responsibility agree about retrying it.
 - It waits the biggest delay stored in the chain of responsibility.

So, technically, all retry strategies accept the next strategy as second constructor argument. Here, an example on
how you should instantiate them:

``` php
Ivory\HttpAdapter\Event\Retry\Strategy\LimitedRetryStrategy;
Ivory\HttpAdapter\Event\Retry\Strategy\LinearDelayedRetryStrategy;

$retryStrategy = new LimitedRetryStrategy(5, new LinearDelayedRetryStrategy(2));
```

Additionally, you can update the chain of responsibility at runtime with the following API:

``` php
$hasNext = $retryStrategy->hasNext();
$next = $retryStrategy->getNext();
$retryStrategy->setNext($next);
```

### Status Code

The status code subscriber is defined by the `Ivory\HttpAdapter\Event\Subscriber\StatusCodeSubscriber` and allow you
to detect errored response. Basically, by default, all http adapters don't throw an exception if a 4xx or 5xx response
is returned. Then, if you want to throw an exception for this kind of response, just register the status code
subscriber:

``` php
use Ivory\HttpAdapter\Event\Subscriber\StatusCodeSubscriber;

$statusCodeSubscriber = new StatusCodeSubscriber();
```

Internally, the status code subscriber uses the `Ivory\HttpAdapter\Event\StatusCode\StatusCode` in order to detect if
a request is valid. By default, if you don't provide it, it will be created automatically. If you want to use your own,
you can pass it via constructor or getter/setter:

``` php
use Ivory\HttpAdapter\Event\StatusCode\StatusCode;
use Ivory\HttpAdapter\Event\Subscriber\StatusCodeSubscriber;

$statusCodeSubscriber = new StatusCodeSubscriber(new StatusCode());

$statusCode = $statusCodeSubscriber->getStatusCode();
$statusCodeSubscriber->setStatusCode($statusCode);
```

### Stopwatch

The stopwatch subscriber is defined by the `Ivory\HttpAdapter\Event\Subscriber\StopwatchSubscriber` and allows you to
time the http adapter sending through the Symfony2 Stopwatch component. To use it:

``` php
use Ivory\HttpAdapter\Event\Subscriber\StopwatchSubscriber;
use Symfony\Component\Stopwatch\Stopwatch;

$stopwatch = new Stopwatch();
$stopwatchSubscriber = new StopwatchSubscriber($stopwatch);
```

You can also change the stopwatch at runtime:

``` php
$stopwatch = $stopwatchSubscriber->getStopwatch();
$stopwatchSubscriber->setStopwatch($stopwatch);
```

## Event Subscriber Priorities

All event subscribers can work together (thanks to the event priorities). Here, the summary:

| Event Subscriber | Pre Send Event | Post Send Event | Exception Event |
| ---------------- | :------------: | :-------------: | :-------------: |
| Stopwatch        | 10000          | 10000           | 10000           |
| Basic Auth       | 300            | -               | -               |
| Cookie           | 300            | 300             | -               |
| Status Code      | -              | 200             | -               |
| History          | 100            | 100             | -               |
| Logger           | 100            | 100             | 100             |
| Redirect         | -              | 0               | -               |
| Retry            | -              | -               | 0               |
