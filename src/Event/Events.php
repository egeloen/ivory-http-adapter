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
    /** @const string The pre send event. */
    const PRE_SEND = 'ivory.http_adapter.pre_send';

    /** @const string The post send event. */
    const POST_SEND = 'ivory.http_adapter.post_send';

    /** @const string The exception event. */
    const EXCEPTION = 'ivory.http_adapter.exception';

    /** @const string The pre send event. */
    const MULTI_PRE_SEND = 'ivory.http_adapter.multi_pre_send';

    /** @const string The post send event. */
    const MULTI_POST_SEND = 'ivory.http_adapter.multi_post_send';

    /** @const string The exception event. */
    const MULTI_EXCEPTION = 'ivory.http_adapter.multi_exception';
}
