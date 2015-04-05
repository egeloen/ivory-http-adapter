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
 * Events.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Events extends AbstractUninstantiableAsset
{
    /** @const string The request created event. */
    const REQUEST_CREATED = 'ivory.http_adapter.request_created';

    /** @const string The request sent event. */
    const REQUEST_SENT = 'ivory.http_adapter.request_sent';

    /** @const string The request errored event. */
    const REQUEST_ERRORED = 'ivory.http_adapter.request_errored';

    /** @const string The multi request created event. */
    const MULTI_REQUEST_CREATED = 'ivory.http_adapter.multi_request_created';

    /** @const string The multi request sent event. */
    const MULTI_REQUEST_SENT = 'ivory.http_adapter.multi_request_sent';

    /** @const string The multi request errored event. */
    const MULTI_REQUEST_ERRORED = 'ivory.http_adapter.multi_request_errored';
}
