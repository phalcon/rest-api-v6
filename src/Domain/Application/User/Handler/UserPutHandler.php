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

use PDOException;
use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Application\User\Command\UserPutCommand;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\Constants\Dates;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;

/**
 * @phpstan-import-type TUserDomainToDbRecord from UserTypes
 * @phpstan-import-type TUserDbRecordOptional from UserTypes
 */
final class UserPutHandler extends AbstractUserPutPostHandler
{
    /**
     * Delete a user.
     *
     * @param CommandInterface $command
     *
     * @return Payload
     */
    public function __invoke(CommandInterface $command): Payload
    {
        $validation = $this->validator->validate($command);
        if (!$validation->isValid()) {
            return Payload::invalid($validation->getErrors());
        }

        /**
         * Check if the user exists, If not, return an error
         */
        /** @var UserPutCommand $command */
        $domainUser = $this->repository->findById($command->id);

        if (null === $domainUser) {
            return Payload::notFound();
        }

        /**
         * Array for updating
         */
        $user = $this->mapper->db($command);

        /**
         * Pre-update checks and manipulations
         */
        $user = $this->preUpdate($user);

        /**
         * Update the record
         */
        try {
            $userId = $this->repository->update($command->id, $user);
        } catch (PDOException $ex) {
            /**
             * Fire the event to the listeners
             */
            $this->eventsManager->fire(
                'user:pdoError',
                $this,
                $ex->getMessage()
            );

            return $this->getErrorPayload(
                HttpCodesEnum::AppCannotUpdateDatabaseRecord,
                $ex->getMessage()
            );
        }

        if ($userId < 1) {
            return $this->getErrorPayload(
                HttpCodesEnum::AppCannotUpdateDatabaseRecord,
                'No id returned'
            );
        }

        /**
         * Get the user from the database
         */
        /** @var User $domainUser */
        $domainUser = $this->repository->findById($userId);

        /**
         * Return the user back
         */
        return Payload::updated($this->transformer->get($domainUser));
    }

    /**
     * @param TUserDomainToDbRecord $input
     *
     * @return TUserDbRecordOptional
     */
    private function preUpdate(array $input): array
    {
        $result = $this->processPassword($input);
        $now    = Dates::toUTC(format: Dates::DATE_TIME_FORMAT);
        /** @var User $sessionUser */
        $sessionUser = $this->registry->get('user');

        $result['usr_updated_usr_id'] = $sessionUser->id;

        /**
         * Set updated date to now if it has not been set
         */
        if (true === empty($result['usr_updated_date'])) {
            $result['usr_updated_date'] = $now;
        }

        /**
         * Remove createdDate and createdUserId - cannot be changed. This
         * needs to be here because we don't want to touch those fields.
         */
        unset($result['usr_created_date'], $result['usr_created_usr_id']);


        return $this->cleanupFields($result);
    }
}
