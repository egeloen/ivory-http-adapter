# UPGRADE

### 0.7 to 0.8

 * The `Ivory\HttpAdapter\GuzzleHttpAdapter` has been renamed to `Ivory\HttpAdapter\Guzzle3HttpAdapter` and its name
   has been renamed from `guzzle` to `guzzle3`.
 * The `Ivory\HttpAdapter\GuzzleHttpHttpAdapter` has been renamed to `Ivory\HttpAdapter\Guzzle5HttpAdapter` and its
   name has been renamed from `guzzle_http` to `guzzle5`.
 * The `Ivory\HttpAdapter\Guzzle4HttpAdapter` is now an alias of thz Guzzle 5 http adapter. Its name is `guzzle4`.
 * The `Ivory\HttpAdapter\HttpAdapterFactory::GUZZLE` constant has been renamed to `GUZZLE3`.
 * The `Ivory\HttpAdapter\HttpAdapterFactory::GUZZLE_HTTP` constant has been renamed to `GUZZLE4` and `GUZZLE5`.
 * The [phly/http](https://github.com/phly/http) has been replaced by
   [zendframework/zend-diactoros](https://github.com/zendframework/zend-diactoros).
 * The `Ivory\HttpAdapter\Message\Stream\AbstractStream`, `Ivory\HttpAdapter\Message\Stream\GuzzleStream` and 
   `Ivory\HttpAdapter\Message\Stream\GuzzleHttpStream` have been removed. 
 * The CakePHP 2.x support has been dropped in favor of the 3.x one.
 * `PreRequest` event renamed to `RequestCreated`.
 * `PostRequest` event renamed to `RequestSent`.
 * `Exception` event renamed to `RequestErrored`.
 * `MultiPreRequest` event renamed to `MultiRequestCreated`.
 * `MultiPostRequest` event renamed to `MultiRequestSent`.
 * `MultiException` event renamed to `MultiRequestErrored`.
 * Event subscribers are now immutable.

### 0.6 to 0.7

 * The lowest PHP version supported has been bumped to 5.4.8+ due to the usage of `phly/http`.
 * The `psr/http-message` has been bumped from `0.5` to `0.9` with plenty of BC breaks.
 * The `Ivory\HttpAdapter\Message` namespace has been rewritten in order to match `phly/http` and `psr/http-message`.
 * The `Ivory\HttpAdapter\AbstractHttpAdapterTemplate` has been removed in favor of the 
   `Ivory\HttpAdapter\HttpAdapterTrait`.
 * The event dispatcher has been moved to a decorator. So, it has been removed from the configuration and all the 
   event related code is not part of the `Ivory\HttpAdapter\AbstractHttpAdapter` anymore but part of the 
   `Ivory\HttpAdapter\EventDispatcherHttpAdapter`.
 * The event model which populated informations into requests or responses now returns the new requests or responses 
   created due to the immutability of these classes.
 * The event http adapter setter has been removed in order to make the http adapter immutable.
 * The `Ivory\HttpAdapter\AbstractHttpAdapter::doSendInternalRequest` and 
   `Ivory\HttpAdapter\AbstractHttpAdapter::doSendInternalRequests` has been renamed respectively to 
   `sendInternalRequest` and `sendInternalRequests` which were the methods previously reserved for the event 
   dispatching.
 * The internal request raw datas has been dropped in favor of the body already available through the extended request.
 * The `Ivory\HttpAdapter\Message\Stream\AbstractStream::doRewind` has been introduced in order to match 
   `psr/http-message`.
 * The `Ivory\HttpAdapter\Message\Stream\ResourceStream` and `Ivory\HttpAdapter\Message\Stream\StringStream` have been 
   removed. You should now rely on the `phly/http` stream.
 * The `Ivory\HttpAdapter\Message\MessageFactory::clone*` methods have been removed (not used anymore) and the 
   `reasonPhrase` parameter of the `Ivory\HttpAdapter\Message\MessageFactory::createResponse` has been removed too 
   (not needed anymore).
 * The `Ivory\HttpAdapter\Extractor\ReasonPhraseExtractor` has been removed (not used anymore).
 * The `Ivory\HttpAdapter\Normalizer\MethodNormalizer` and `Ivory\HttpAdapter\Normalizer\UrlNormalizer` have been 
   removed (not used anymore).
 * All `url` have been renamed to `uri` (including properties and methods) in order to match `psr/http-message`.

### 0.5 to 0.6

 * The `Ivory\HttpAdapter\Event` namespace has been rewrite and so, it is plenty of BC breaks... Basically, lot of
   logic stored in event subscribers have been moved to dedicated classes so, it works pretty the same way but not
   exactly... If you're using them, it will be really easy to migrate your app so, please read the new documentation
   related to events.
 * The `symfony/event-dispatcher` is now optional. Accordingly, the  `Ivory\HttpAdapter\Configuration::$eventDispatcher`
   has been made optional too and the `hasEventSubscriber` method has been introduced.
 * The `Ivory\HttpAdapter\AbstractHttpAdapter::doSend` has been renamed to `doSendInternalRequest`.
 * The `Ivory\HttpAdapter\AbstractHttpAdapter::createResponse` has been removed. You should rely on
   `Ivory\HttpAdapter\Message\MessageFactory::createResponse` instead.
 * The `Ivory\HttpAdapter\HttpAdapterInterface::sendInternalRequest` has been removed. You should rely on
  `Ivory\HttpAdapter\HttpAdapterInterface::sendRequest` instead.
 * All protected properties and methods have been updated to private except for entry points. This is mostly motivated
   for enforcing the encapsulation and easing backward compatibility.

### 0.4 to 0.5

 * The `Ivory\HttpAdapter\Guzzle3HttpAdapter` has been renamed to `Ivory\HttpAdapter\GuzzleHttpAdapter` and its name
   has been renamed to from `guzzle3` to `guzzle` as well.

 * The `Ivory\HttpAdapter\Guzzle4HttpAdapter` has been renamed to `Ivory\HttpAdapter\GuzzleHttpHttpAdapter`, its
   name has been renamed to from `guzzle4` to `guzzle_http` and it now supports Guzzle 5.

 * The `Ivory\HttpAdapter\Message\Stream\Guzzle3Stream` has been renamed to
   `Ivory\HttpAdapter\Message\Stream\GuzzleStream`.

 * The `Ivory\HttpAdapter\Message\Stream\Guzzle4Stream` has been renamed to
   `Ivory\HttpAdapter\Message\Stream\GuzzleHttpStream` and it now supports Guzzle 5.

### 0.3 to 0.4

 * The PSR HTTP message dependency has break the backward compatibility.
   See https://github.com/php-fig/http-message/compare/0.3.0...0.4.0

### 0.2 to 0.3

 * The PSR HTTP message dependency has break the backward compatibility.
   See https://github.com/php-fig/http-message/compare/0.2.0...0.3.0

### 0.1 to 0.2

 * The PSR HTTP message dependency has break the backward compatibility.
   See https://github.com/php-fig/http-message/compare/0.1.0...0.2.0

 * The `Ivory\HttpAdapter\Message\Stream\AbstractStream` has two new abstract methods (`doAttach` and `doGetMetadata`)
   which have been added in order to reflect the PSR HTTP message changes.

 * The `Ivory\HttpAdapter\HttpAdapterException::resourceIsNotValid` has been renamed to `streamIsNotValid` and it now
   takes the stream as first parameter, the wrapper as second parameter and the expected stream as third parameter.

 * The `Ivory\HttpAdapter\Message\Stream\ResourceSteam::$isReadable`,
   `Ivory\HttpAdapter\Message\Stream\ResourceSteam::$isWritable`,
   `Ivory\HttpAdapter\Message\Stream\ResourceSteam::$isSeekable` and
   `Ivory\HttpAdapter\Message\Stream\ResourceSteam::$isLocal` properties have been removed as it does not bring any
   values and are only used internally.

 * The `Ivory\HttpAdapter\Message\Stream\ResourceSteam::$modes` structure has been simplified.

 * The `Ivory\HttpAdapter\Message\Stream\ResourceSteam::isLocal`,
   `Ivory\HttpAdapter\Message\Stream\ResourceSteam::buildCache` and
   `Ivory\HttpAdapter\Message\Stream\ResourceSteam::clearCache` methods have been removed.
