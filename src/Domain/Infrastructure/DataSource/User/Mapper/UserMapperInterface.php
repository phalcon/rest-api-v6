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

use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;

/**
 * Contract for mapping between domain DTO/objects and persistence arrays.
 *
 * @phpstan-import-type TUser from UserTypes
 * @phpstan-import-type TUserDbRecord from UserTypes
 * @phpstan-import-type TUserDomainToDbRecord from UserTypes
 */
interface UserMapperInterface
{
    /**
     * Map Domain User -> DB row (usr_*)
     *
     * @return TUserDomainToDbRecord
     */
    public function db(CommandInterface $user): array;

    /**
     * Map DB row (usr_*) -> Domain User
     *
     * @param TUserDbRecord|array{} $row
     */
    public function domain(array $row): User;
}
