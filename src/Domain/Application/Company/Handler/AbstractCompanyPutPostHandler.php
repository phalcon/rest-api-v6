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
use Phalcon\Api\Domain\Infrastructure\CommandBus\HandlerInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\Company;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper\CompanyMapperInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Repository\CompanyRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Transformer\Transformer;
use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\ValidatorInterface;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;
use Phalcon\Support\Registry;

use function array_filter;

/**
 * @phpstan-import-type TCompanyDomainToDbRecord from CompanyTypes
 * @phpstan-import-type TCompanyDbRecordOptional from CompanyTypes
 */
abstract class AbstractCompanyPutPostHandler implements HandlerInterface
{
    /**
     * @param ValidatorInterface         $validator
     * @param CompanyMapperInterface     $mapper
     * @param CompanyRepositoryInterface $repository
     * @param EventsManagerInterface     $eventsManager
     * @param Transformer<Company>       $transformer
     * @param Registry                   $registry
     */
    public function __construct(
        protected readonly ValidatorInterface $validator,
        protected readonly CompanyMapperInterface $mapper,
        protected readonly CompanyRepositoryInterface $repository,
        protected readonly EventsManagerInterface $eventsManager,
        protected readonly Transformer $transformer,
        protected readonly Registry $registry,
    ) {
    }

    /**
     * @param TCompanyDbRecordOptional $row
     *
     * @return TCompanyDbRecordOptional
     */
    protected function cleanupFields(array $row): array
    {
        unset($row['com_id']);

        return array_filter(
            $row,
            static fn($v) => $v !== null && $v !== ''
        );
    }

    /**
     * @param HttpCodesEnum $item
     * @param string        $message
     *
     * @return Payload
     */
    protected function getErrorPayload(
        HttpCodesEnum $item,
        string $message
    ): Payload {
        return Payload::error([[$item->text() . $message]]);
    }
}
