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

/**
 * @phpstan-type TResultItem array<array-key, bool|int|string>
 * @phpstan-type TResult array{
 *     result: array<array-key, bool|int|string|TResultItem>
 * }
 * @phpstan-type TData array<array-key, mixed>|array{}
 * @phpstan-type TErrors array<array-key, array<int, string>>|array{}
 * @phpstan-type TResponsePayload array{
 *       data: TData,
 *       errors: TErrors,
 *       meta: array{
 *           code: int,
 *           hash: string,
 *           message: string,
 *           timestamp: string
 *      }
 *  }
 */
final class ResponderTypes
{
}
