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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\AbstractInputValueObject;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;

use function abs;

/**
 * Value object for input
 *
 * @phpstan-import-type TCompanyInput from InputTypes
 * @phpstan-import-type TCompany from CompanyTypes
 */
final class CompanyInput extends AbstractInputValueObject
{
    /**
     * @param int         $id
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
        public readonly int $id,
        public readonly ?string $name,
        public readonly ?string $phone,
        public readonly ?string $email,
        public readonly ?string $website,
        public readonly ?string $addressLine1,
        public readonly ?string $addressLine2,
        public readonly ?string $city,
        public readonly ?string $stateProvince,
        public readonly ?string $zipCode,
        public readonly ?string $country,
        public readonly ?string $createdDate,
        public readonly ?int $createdUserId,
        public readonly ?string $updatedDate,
        public readonly ?int $updatedUserId,
        public readonly int $page,
        public readonly int $perPage,
    ) {
    }

    /**
     * Build the concrete DTO from a sanitized array.
     *
     * @param TCompanyInput $sanitized
     *
     * @return static
     */
    protected static function fromArray(array $sanitized): static
    {
        return new self(
            abs(isset($sanitized['id']) ? (int)$sanitized['id'] : 0),
            isset($sanitized['name']) ? (string)$sanitized['name'] : null,
            isset($sanitized['phone']) ? (string)$sanitized['phone'] : null,
            isset($sanitized['email']) ? (string)$sanitized['email'] : null,
            isset($sanitized['website']) ? (string)$sanitized['website'] : null,
            isset($sanitized['addressLine1']) ? (string)$sanitized['addressLine1'] : null,
            isset($sanitized['addressLine2']) ? (string)$sanitized['addressLine2'] : null,
            isset($sanitized['city']) ? (string)$sanitized['city'] : null,
            isset($sanitized['stateProvince']) ? (string)$sanitized['stateProvince'] : null,
            isset($sanitized['zipCode']) ? (string)$sanitized['zipCode'] : null,
            isset($sanitized['country']) ? (string)$sanitized['country'] : null,
            isset($sanitized['createdDate']) ? (string)$sanitized['createdDate'] : null,
            abs(isset($sanitized['createdUserId']) ? (int)$sanitized['createdUserId'] : 0),
            isset($sanitized['updatedDate']) ? (string)$sanitized['updatedDate'] : null,
            abs(isset($sanitized['updatedUserId']) ? (int)$sanitized['updatedUserId'] : 0),
            abs(isset($sanitized['page']) ? (int)$sanitized['page'] : 1),
            abs(isset($sanitized['perPage']) ? (int)$sanitized['perPage'] : 10)
        );
    }
}
