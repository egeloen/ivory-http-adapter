# UPGRADE

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
