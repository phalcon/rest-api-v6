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

namespace Phalcon\Api\Domain\Application\User\Command;

use Phalcon\Api\Domain\ADR\InputTypes;

/**
 * @phpstan-import-type TUserInput from InputTypes
 */
interface UserCommandFactoryInterface
{
    /**
     * @param TUserInput $input
     *
     * @return UserDeleteCommand
     */
    public function delete(array $input): UserDeleteCommand;

    /**
     * @param TUserInput $input
     *
     * @return UserGetCommand
     */
    public function get(array $input): UserGetCommand;

    /**
     * @param TUserInput $input
     *
     * @return UserPostCommand
     */
    public function insert(array $input): UserPostCommand;

    /**
     * @param TUserInput $input
     *
     * @return UserPutCommand
     */
    public function update(array $input): UserPutCommand;
}
