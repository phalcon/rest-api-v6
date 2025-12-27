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

namespace Phalcon\Api\Domain\Application\Company\Command;

use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;

final readonly class CompanyGetManyCommand implements CommandInterface
{
    /**
     * @param int|null    $id
     * @param string|null $name
     * @param string|null $phone
     * @param string|null $email
     * @param string|null $website
     * @param string|null $addressLine1
     * @param string|null $addressLine2
     * @param string|null $city
     * @param string|null $stateProvince
     * @param string|null $zipCode
     * @param string|null $country
     * @param string|null $createdDate
     * @param int|null    $createdUserId
     * @param string|null $updatedDate
     * @param int|null    $updatedUserId
     * @param int         $page
     * @param int         $perPage
     */
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $phone,
        public ?string $email,
        public ?string $website,
        public ?string $addressLine1,
        public ?string $addressLine2,
        public ?string $city,
        public ?string $stateProvince,
        public ?string $zipCode,
        public ?string $country,
        public ?string $createdDate,
        public ?int $createdUserId,
        public ?string $updatedDate,
        public ?int $updatedUserId,
        public int $page,
        public int $perPage,
    ) {
    }
}
