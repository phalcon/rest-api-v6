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

use Phalcon\Api\Domain\Infrastructure\DataSource\AbstractValueObject;

final class Company extends AbstractValueObject
{
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
    ) {
    }
}
