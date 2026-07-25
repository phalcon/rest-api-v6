<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Action\Auth;

use Phalcon\Api\Action\AbstractAction;

/**
 * POST /auth/login
 *
 * The name is what the convention router looks for; changing it changes
 * the URL. Behavior lives in AbstractAction - the container pins the
 * service this action delegates to.
 *
 */
final class PostAuthLogin extends AbstractAction
{
}
