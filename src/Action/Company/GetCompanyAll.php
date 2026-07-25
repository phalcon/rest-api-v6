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

namespace Phalcon\Api\Action\Company;

use Phalcon\Api\Action\AbstractAction;

/**
 * GET /company/all
 *
 * Matched ahead of GetCompany because the router tries the longer
 * two-segment name first; /company/1 falls through to GetCompany.
 *
 */
final class GetCompanyAll extends AbstractAction
{
}
