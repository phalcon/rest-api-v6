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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\User;

/**
 * @phpstan-type TLoginResponsePayload array{
 *      authenticated: true,
 *      user: array{
 *          id: int,
 *          name: string,
 *          email: string
 *      },
 *      jwt: array{
 *         token: string,
 *         refreshToken: string,
 *      }
 * }
 *
 * @phpstan-type TUserDbRecord array{
 *     usr_id: int,
 *     usr_status_flag: int,
 *     usr_email: string,
 *     usr_password: string,
 *     usr_name_prefix: string,
 *     usr_name_first: string,
 *     usr_name_middle: string,
 *     usr_name_last: string,
 *     usr_name_suffix: string,
 *     usr_issuer: string,
 *     usr_token_password: string,
 *     usr_token_id: string,
 *     usr_preferences: string,
 *     usr_created_date: string,
 *     usr_created_usr_id: int,
 *     usr_updated_date: string,
 *     usr_updated_usr_id: int,
 * }
 *
 * @phpstan-type TUserDomainToDbRecord array{
 *     usr_id: int,
 *     usr_status_flag: int,
 *     usr_email: ?string,
 *     usr_password: ?string,
 *     usr_name_prefix: ?string,
 *     usr_name_first: ?string,
 *     usr_name_middle: ?string,
 *     usr_name_last: ?string,
 *     usr_name_suffix: ?string,
 *     usr_issuer: ?string,
 *     usr_token_password: ?string,
 *     usr_token_id: ?string,
 *     usr_preferences: ?string,
 *     usr_created_date: ?string,
 *     usr_created_usr_id: ?int,
 *     usr_updated_date: ?string,
 *     usr_updated_usr_id: ?int,
 * }
 *
 * @phpstan-type TUserDbRecordOptional array{
 *     usr_id?: int,
 *     usr_status_flag?: int,
 *     usr_email?: ?string,
 *     usr_password?: ?string,
 *     usr_name_prefix?: ?string,
 *     usr_name_first?: ?string,
 *     usr_name_middle?: ?string,
 *     usr_name_last?: ?string,
 *     usr_name_suffix?: ?string,
 *     usr_issuer?: ?string,
 *     usr_token_password?: ?string,
 *     usr_token_id?: ?string,
 *     usr_preferences?: ?string,
 *     usr_created_date?: ?string,
 *     usr_created_usr_id?: ?int,
 *     usr_updated_date?: ?string,
 *     usr_updated_usr_id?: ?int,
 * }
 *
 * @phpstan-type TUser array{
 *     id: int,
 *     status: int,
 *     email: ?string,
 *     password: ?string,
 *     namePrefix: ?string,
 *     nameFirst: ?string,
 *     nameMiddle: ?string,
 *     nameLast: ?string,
 *     nameSuffix: ?string,
 *     issuer: ?string,
 *     tokenPassword: ?string,
 *     tokenId: ?string,
 *     preferences: ?string,
 *     createdDate: ?string,
 *     createdUserId: ?int,
 *     updatedDate: ?string,
 *     updatedUserId: ?int,
 * }
 *
 * @phpstan-type TUserTokenDbRecord array{
 *     usr_id: int,
 *     usr_issuer: string,
 *     usr_token_password: string,
 *     usr_token_id: string
 * }
 *
 * @phpstan-type TUserRecord array{}|TUserDbRecord
 *
 * @phpstan-type TCriteria array<string, bool|int|string|null>
 */
final class UserTypes
{
}
