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

namespace Phalcon\Api\Domain\Application\Company\Handler;

use PDOException;
use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Application\Company\Command\CompanyPutCommand;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\Constants\Dates;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\Company;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;

/**
 * @phpstan-import-type TCompanyDomainToDbRecord from CompanyTypes
 * @phpstan-import-type TCompanyDbRecordOptional from CompanyTypes
 */
final class CompanyPutHandler extends AbstractCompanyPutPostHandler
{
    /**
     * Delete a company.
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
         * Check if the company exists, If not, return an error
         */
        /** @var CompanyPutCommand $command */
        $domainCompany = $this->repository->findById($command->id);

        if (null === $domainCompany) {
            return Payload::notFound();
        }

        /**
         * Array for updating
         */
        $company = $this->mapper->db($command);

        /**
         * Pre-update checks and manipulations
         */
        $company = $this->preUpdate($company);

        /**
         * Update the record
         */
        try {
            $companyId = $this->repository->update($command->id, $company);
        } catch (PDOException $ex) {
            /**
             * Fire the event to the listeners
             */
            $this->eventsManager->fire(
                'company:pdoError',
                $this,
                $ex->getMessage()
            );

            return $this->getErrorPayload(
                HttpCodesEnum::AppCannotUpdateDatabaseRecord,
                $ex->getMessage()
            );
        }

        if ($companyId < 1) {
            return $this->getErrorPayload(
                HttpCodesEnum::AppCannotUpdateDatabaseRecord,
                'No id returned'
            );
        }

        /**
         * Get the company from the database
         */
        /** @var Company $domainCompany */
        $domainCompany = $this->repository->findById($companyId);

        /**
         * Return the company back
         */
        return Payload::updated($this->transformer->get($domainCompany));
    }

    /**
     * @param TCompanyDomainToDbRecord $input
     *
     * @return TCompanyDbRecordOptional
     */
    private function preUpdate(array $input): array
    {
        $now = Dates::toUTC(format: Dates::DATE_TIME_FORMAT);
        /** @var User $sessionUser */
        $sessionUser = $this->registry->get('user');

        $input['com_updated_usr_id'] = $sessionUser->id;

        /**
         * Set updated date to now if it has not been set
         */
        if (true === empty($input['com_updated_date'])) {
            $input['com_updated_date'] = $now;
        }

        /**
         * Remove createdDate and createdCompanyId - cannot be changed. This
         * needs to be here because we don't want to touch those fields.
         */
        unset($input['com_created_date'], $input['com_created_usr_id']);


        return $this->cleanupFields($input);
    }
}
