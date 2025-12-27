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
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\Constants\Dates;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;

/**
 * @phpstan-import-type TUserDomainToDbRecord from UserTypes
 * @phpstan-import-type TUserDbRecordOptional from UserTypes
 */
final class UserPostHandler extends AbstractUserPutPostHandler
{
    /**
     * Create a user.
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
         * Array for inserting
         */
        $user = $this->mapper->db($command);

        /**
         * Pre-insert checks and manipulations
         */
        $user = $this->preInsert($user);

        /**
         * Insert the record
         */
        try {
            $userId = $this->repository->insert($user);
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
                HttpCodesEnum::AppCannotCreateDatabaseRecord,
                $ex->getMessage()
            );
        }

        if ($userId < 1) {
            return $this->getErrorPayload(
                HttpCodesEnum::AppCannotCreateDatabaseRecord,
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
        return Payload::created($this->transformer->get($domainUser));
    }

    /**
     * @param TUserDomainToDbRecord $input
     *
     * @return TUserDbRecordOptional
     */
    private function preInsert(array $input): array
    {
        $result = $this->processPassword($input);
        $now    = Dates::toUTC(format: Dates::DATE_TIME_FORMAT);
        /** @var User $sessionUser */
        $sessionUser = $this->registry->get('user');

        /**
         * Created and updated user IDs come from the user in the session
         */
        $result['usr_created_usr_id'] = $sessionUser->id;
        $result['usr_updated_usr_id'] = $sessionUser->id;

        /**
         * Set the created/updated dates if need be
         */
        if (true === empty($result['usr_created_date'])) {
            $result['usr_created_date'] = $now;
        }
        if (true === empty($result['usr_updated_date'])) {
            $result['usr_updated_date'] = $now;
        }

        /** @var TUserDbRecordOptional $result */
        return $this->cleanupFields($result);
    }
}
