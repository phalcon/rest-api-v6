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

use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Http\ResponseInterface;

interface ResponderInterface
{
    public function __invoke(
        ResponseInterface $response,
        Payload $payload
    ): ResponseInterface;
}
