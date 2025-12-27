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
use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\SanitizerInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\UserInput;

/**
 * @phpstan-import-type TUserInput from InputTypes
 */
final readonly class UserCommandFactory implements UserCommandFactoryInterface
{
    public function __construct(
        private SanitizerInterface $sanitizer
    ) {
    }

    /**
     * @param TUserInput $input
     *
     * @return UserDeleteCommand
     */
    public function delete(array $input): UserDeleteCommand
    {
        /**
         * Sanitize the input
         */
        $dto = UserInput::new($this->sanitizer, $input);

        /**
         * Return the DTO back
         */
        return new UserDeleteCommand($dto->id);
    }

    /**
     * @param TUserInput $input
     *
     * @return UserGetCommand
     */
    public function get(array $input): UserGetCommand
    {
        /**
         * Sanitize the input
         */
        $dto = UserInput::new($this->sanitizer, $input);

        /**
         * Return the DTO back
         */
        return new UserGetCommand($dto->id);
    }

    /**
     * @param TUserInput $input
     *
     * @return UserPostCommand
     */
    public function insert(array $input): UserPostCommand
    {
        /**
         * Sanitize the input
         */
        $dto = UserInput::new($this->sanitizer, $input);

        /**
         * Return the DTO back
         */
        return new UserPostCommand(
            null,
            $dto->status,
            $dto->email,
            $dto->password,
            $dto->namePrefix,
            $dto->nameFirst,
            $dto->nameMiddle,
            $dto->nameLast,
            $dto->nameSuffix,
            $dto->issuer,
            $dto->tokenPassword,
            $dto->tokenId,
            $dto->preferences,
            $dto->createdDate,
            $dto->createdUserId,
            $dto->updatedDate,
            $dto->updatedUserId,
        );
    }

    /**
     * @param TUserInput $input
     *
     * @return UserPutCommand
     */
    public function update(array $input): UserPutCommand
    {
        /**
         * Sanitize the input
         */
        $dto = UserInput::new($this->sanitizer, $input);

        /**
         * Return the DTO back
         */
        return new UserPutCommand(
            $dto->id,
            $dto->status,
            $dto->email,
            $dto->password,
            $dto->namePrefix,
            $dto->nameFirst,
            $dto->nameMiddle,
            $dto->nameLast,
            $dto->nameSuffix,
            $dto->issuer,
            $dto->tokenPassword,
            $dto->tokenId,
            $dto->preferences,
            $dto->createdDate,
            $dto->createdUserId,
            $dto->updatedDate,
            $dto->updatedUserId,
        );
    }
}
