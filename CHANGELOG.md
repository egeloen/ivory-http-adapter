# CHANGELOG

### 0.6.0 (2015-02-10)

 * cd7e18b - [HttpAdapterFactory] Added a guess and capable methods
 * 21055c2 - Add parallel requests support
 * c0fcdfd - Add base url support
 * 77173c4 - Make http_buil_query independant of arg_separator.output ini setting
 * bbcf51a - Introduce Version
 * 1b15d7b - [Event] Make timer subscriber stateless
 * f16767a - Set request/response on exception before passing it to exception listeners
 * 613132a - [Test] Reintroduce send internal request tests
 * 7eb424b - Rename AbstractHttpAdapter::doSend to doSendInternalRequest
 * b91f40f - Remove AbstractHttpAdapter::createResponse and rely on MessageFactory::createResponse instead
 * a260489 - [Travis] Move Symfony 2.6.*@dev to 2.6.*
 * 605d943 - Remove HttpAdapterInterface::sendInternalRequest method
 * 4c33b6c - [Travis] Update config
 * 90c58a3 - Add .gitattributes
 * dfe5877 - Add CakePHP http adapter
 * dc3fa00 - Add ReactPHP http adapter
 * 5145b12 - Introduce http adapter factory
 * c918dbf - [Composer] Rely on autoload-dev
 * a88709c - [Test] Rename debug file to ivory-http-adapter.log
 * d00e1f7 - [Test] Add Apache 2.4 compatibility
 * e4f6efb - Add Symfony2 stopwatch support
 * cc3f5f2 - [Event][Retry] Fix verify for limited retry strategy
 * 6e10883 - [Encapsulation] Move everything from protected to private (except for entry point)

### 0.5.0 (2014-11-05)

 * 4523e62 - [Message] Update according to PSR HTTP message 0.5.0 BC breaks
 * 4e0e387 - [Composer] Refine dependency
 * 198b6f4 - Add Guzzle 5 support
 * a2940f6 - [Exception] Add related request/response if available

### 0.4.0 (2014-10-26)

 * 75e5f69 - [Message] Update according to PSR HTTP message 0.4.0 BC breaks

### 0.3.0 (2014-10-26)

 * 65ad8ba - [Message] Update according to PSR HTTP message 0.3.0 BC breaks

### 0.2.0 (2014-10-26)

 * 4cbd0b5 - [Message] Update according to PSR HTTP message 0.2.0 BC breaks

### 0.1.2 (2014-10-25)

 * 6527485 - [Stream] Fix some returns + PHPDoc

### 0.1.1 (2014-10-25)

 * 51ac68c - [Test] Remove http adapter file after execution
 * 9c1382a - [Stream] Fix string stream
 * 8478738 - [StringStream] Fix boolean return
 * 50871fe - [Test] Lock share file
 * 9a77280 - [Curl] Reduce timeout code duplication
 * ceddaf2 - [Composer] Refine dependencies
 * 704188f - Normalize timeout handling
 * 6238854 - Typehint PSR request + CS Fixes + Refine Guzzle4 dependency

### 0.1.0 (2014-10-03)
