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
final class CompanyPostHandler extends AbstractCompanyPutPostHandler
{
    /**
     * Create a company.
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
        $company = $this->mapper->db($command);

        /**
         * Pre-insert checks and manipulations
         */
        $company = $this->preInsert($company);

        /**
         * Insert the record
         */
        try {
            $companyId = $this->repository->insert($company);
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
                HttpCodesEnum::AppCannotCreateDatabaseRecord,
                $ex->getMessage()
            );
        }

        if ($companyId < 1) {
            return $this->getErrorPayload(
                HttpCodesEnum::AppCannotCreateDatabaseRecord,
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
        return Payload::created($this->transformer->get($domainCompany));
    }

    /**
     * @param TCompanyDomainToDbRecord $input
     *
     * @return TCompanyDbRecordOptional
     */
    private function preInsert(array $input): array
    {
        $now = Dates::toUTC(format: Dates::DATE_TIME_FORMAT);
        /** @var User $sessionUser */
        $sessionUser = $this->registry->get('user');

        /**
         * Created and updated company IDs come from the company in the session
         */
        $input['com_created_usr_id'] = $sessionUser->id;
        $input['com_updated_usr_id'] = $sessionUser->id;

        /**
         * Set the created/updated dates if need be
         */
        if (true === empty($input['com_created_date'])) {
            $input['com_created_date'] = $now;
        }
        if (true === empty($input['com_updated_date'])) {
            $input['com_updated_date'] = $now;
        }

        /** @var TCompanyDbRecordOptional $input */
        return $this->cleanupFields($input);
    }
}
