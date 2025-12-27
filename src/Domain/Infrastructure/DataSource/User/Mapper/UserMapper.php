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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper;

use Phalcon\Api\Domain\Application\User\Command\UserPutCommand;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;

/**
 * @phpstan-import-type TUser from UserTypes
 * @phpstan-import-type TUserDbRecord from UserTypes
 * @phpstan-import-type TUserDomainToDbRecord from UserTypes
 */
final class UserMapper implements UserMapperInterface
{
    /**
     * Map Domain User -> DB row (usr_*)
     *
     * @param CommandInterface $user
     *
     * @return TUserDomainToDbRecord
     */
    public function db(CommandInterface $user): array
    {
        /** @var UserPutCommand $user */
        return [
            'usr_id'             => $user->id,
            'usr_status_flag'    => $user->status,
            'usr_email'          => $user->email,
            'usr_password'       => $user->password,
            'usr_name_prefix'    => $user->namePrefix,
            'usr_name_first'     => $user->nameFirst,
            'usr_name_middle'    => $user->nameMiddle,
            'usr_name_last'      => $user->nameLast,
            'usr_name_suffix'    => $user->nameSuffix,
            'usr_issuer'         => $user->issuer,
            'usr_token_password' => $user->tokenPassword,
            'usr_token_id'       => $user->tokenId,
            'usr_preferences'    => $user->preferences,
            'usr_created_date'   => $user->createdDate,
            'usr_created_usr_id' => $user->createdUserId,
            'usr_updated_date'   => $user->updatedDate,
            'usr_updated_usr_id' => $user->updatedUserId,
        ];
    }

    /**
     * Map DB row (usr_*) -> Domain User
     *
     * @param TUserDbRecord|array{} $row
     */
    public function domain(array $row): User
    {
        return new User(
            (int)($row['usr_id'] ?? 0),
            (int)($row['usr_status_flag'] ?? 0),
            (string)($row['usr_email'] ?? ''),
            $row['usr_password'] ?? '',
            $row['usr_name_prefix'] ?? null,
            $row['usr_name_first'] ?? null,
            $row['usr_name_middle'] ?? null,
            $row['usr_name_last'] ?? null,
            $row['usr_name_suffix'] ?? null,
            $row['usr_issuer'] ?? null,
            $row['usr_token_password'] ?? null,
            $row['usr_token_id'] ?? null,
            $row['usr_preferences'] ?? null,
            $row['usr_created_date'] ?? null,
            isset($row['usr_created_usr_id']) ? (int)$row['usr_created_usr_id'] : null,
            $row['usr_updated_date'] ?? null,
            isset($row['usr_updated_usr_id']) ? (int)$row['usr_updated_usr_id'] : null,
        );
    }
}
