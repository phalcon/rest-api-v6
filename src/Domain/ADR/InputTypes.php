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

namespace Phalcon\Api\Domain\ADR;

/**
 * @phpstan-type TAuthInput TAuthLoginInput|TAuthLogoutInput
 *
 * @phpstan-type TAuthLoginInput array{
 *     email?: string,
 *     password?: string
 * }
 *
 * @phpstan-type TAuthLogoutInput array{
 *     token?: string
 * }
 *
 * @phpstan-type TAuthRefreshInput array{
 *     token?: string
 * }
 *
 * @phpstan-type TCompanyInput array{
 *     id?: int,
 *     name?: string,
 *     phone?: string,
 *     email?: string,
 *     website?: string,
 *     addressLine1?: string,
 *     addressLine2?: string,
 *     city?: string,
 *     stateProvince?: string,
 *     zipCode?: string,
 *     country?: string,
 *     createdDate?: string,
 *     createdUserId?: int,
 *     updatedDate?: string,
 *     updatedUserId?: int,
 *     page?: int,
 *     perPage?: int
 * }
 *
 * @phpstan-type TUserInput array{
 *     id?: int,
 *     status?: int,
 *     email?: string,
 *     password?: string,
 *     namePrefix?: string,
 *     nameFirst?: string,
 *     nameMiddle?: string,
 *     nameLast?: string,
 *     nameSuffix?: string,
 *     issuer?: string,
 *     tokenPassword?: string,
 *     tokenId?: string,
 *     preferences?: string,
 *     createdDate?: string,
 *     createdUserId?: int,
 *     updatedDate?: string,
 *     updatedUserId?: int,
 * }
 *
 * @phpstan-type TRequestQuery array<array-key, bool|int|string>
 * @phpstan-type TValidatorErrors array{}|array<int, array<int, string>>
 * @phpstan-type TInputSanitize TUserInput|TAuthInput
 */
final class InputTypes
{
}
