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

namespace Phalcon\Api\Responder;

use Phalcon\ADR\Payload\Status;
use Phalcon\ADR\Responder\StatusMapper;

/**
 * Builds the status-to-HTTP-code map, with three deliberate deviations.
 *
 * Before the move to the native payload, only notFound() and unauthorized()
 * baked an HTTP code into the response; everything else fell through to 200.
 * That means a validation failure, a server error and a creation all answer
 * 200 today. The ADR defaults answer 422, 500 and 201 respectively.
 *
 * Those defaults are correct and this application's behaviour is not. The
 * overrides below preserve the current responses so the ADR migration
 * changes no observable behaviour, and any later regression can be
 * attributed to the migration rather than to a contract change.
 *
 * PHASE 3 removes these three overrides and adds the Location header that a
 * 201 response should carry. That is a deliberate, documented API change.
 *
 * A factory rather than a subclass because StatusMapper is final.
 */
final class ApiStatusMapper
{
    public static function create(): StatusMapper
    {
        return new StatusMapper(
            [
                Status::CREATED   => 200,
                Status::ERROR     => 200,
                Status::NOT_VALID => 200,
            ]
        );
    }
}
