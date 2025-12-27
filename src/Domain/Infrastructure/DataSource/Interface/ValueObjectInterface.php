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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Interface;

use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;

/**
 * @phpstan-import-type TCompany from CompanyTypes
 * @phpstan-import-type TUser from UserTypes
 */
interface ValueObjectInterface
{
    /**
     * @return TCompany|TUser
     */
    public function toArray(): array;
}
