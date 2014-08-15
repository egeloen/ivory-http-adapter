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

### History

The history subscriber is defined by the `Ivory\HttpAdapter\Event\Subscriber\HistorySubscriber` and allow you to
maintain an history of all requests/responses sent through a journal. To use it:

``` php
use Ivory\HttpAdapter\Event\Subscriber\HistorySubscriber;

$historySubscriber = new HistorySubscriber();

$httpAdapter->getEventDispatcher()->addSubscriber($historySubscriber);
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
can be configured via the constructor or setter and can be accessed via a getter:

``` php
use Ivory\HttpAdapter\Event\Subscriber\History\Journal;

$journal = new Journal();
// or
$journal = new Journal(10);

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

Third, a journal is responsible to create its entries. Basically, it acts as a factory and so, if you want to define
your own journal entry, you will need to override the following method:

``` php
$this->journal->record($request, $response, $time);
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
your own implementation and override the `Ivory\HttpAdapter\Event\Subscriber\History\JournalInterface::record` method
in order to instantiate it as explain previously.

It wraps the request, the response and the request execution time. To get/set them, you can use the following API:

``` php
use Ivory\HttpAdapter\Event\Subscriber\History\JournalEntry;

$entry = new JournalEntry($request, $response, $time);

$request = $entry->getRequest();
$entry->setRequest($request);

$response = $entry->getResponse();
$entry->setResponse($response);

$time = $entry->getTime();
$entry->setTime($time);
```
