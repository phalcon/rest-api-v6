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

namespace Phalcon\Api\Domain\Application\User\Handler;

use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Application\User\Command\UserDeleteCommand;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\CommandBus\HandlerInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Transformer\Transformer;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepositoryInterface;

final readonly class UserDeleteHandler implements HandlerInterface
{
    /**
     * @param UserRepositoryInterface $repository
     * @param Transformer<User>       $transformer
     */
    public function __construct(
        private UserRepositoryInterface $repository,
        private Transformer $transformer
    ) {
    }

    /**
     * Delete a user.
     *
     * @param CommandInterface $command
     *
     * @return Payload
     */
    public function __invoke(CommandInterface $command): Payload
    {
        /** @var UserDeleteCommand $command */
        $userId = $command->id;

        /**
         * Success
         */
        if ($userId > 0) {
            $rowCount = $this->repository->deleteById($userId);

            if (0 !== $rowCount) {
                return Payload::deleted($this->transformer->delete($userId));
            }
        }

        /**
         * 404
         */
        return Payload::notFound();
    }
}
