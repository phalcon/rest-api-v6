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

use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Application\Company\Command\CompanyDeleteCommand;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\CommandBus\HandlerInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\Company;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Repository\CompanyRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Transformer\Transformer;

final readonly class CompanyDeleteHandler implements HandlerInterface
{
    /**
     * @param CompanyRepositoryInterface $repository
     * @param Transformer<Company>       $transformer
     */
    public function __construct(
        private CompanyRepositoryInterface $repository,
        private Transformer $transformer
    ) {
    }

    /**
     * Delete a company.
     *
     * @param CommandInterface $command
     *
     * @return Payload
     */
    public function __invoke(CommandInterface $command): Payload
    {
        /** @var CompanyDeleteCommand $command */
        $companyId = $command->id;

        /**
         * Success
         */
        if ($companyId > 0) {
            $rowCount = $this->repository->deleteById($companyId);

            if (0 !== $rowCount) {
                return Payload::deleted($this->transformer->delete($companyId));
            }
        }

        /**
         * 404
         */
        return Payload::notFound();
    }
}
