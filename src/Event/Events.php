<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event;

use Ivory\HttpAdapter\Asset\AbstractUninstantiableAsset;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Events extends AbstractUninstantiableAsset
{
    const REQUEST_CREATED = 'ivory.http_adapter.request_created';
    const REQUEST_SENT = 'ivory.http_adapter.request_sent';
    const REQUEST_ERRORED = 'ivory.http_adapter.request_errored';
    const MULTI_REQUEST_CREATED = 'ivory.http_adapter.multi_request_created';
    const MULTI_REQUEST_SENT = 'ivory.http_adapter.multi_request_sent';
    const MULTI_REQUEST_ERRORED = 'ivory.http_adapter.multi_request_errored';
}
