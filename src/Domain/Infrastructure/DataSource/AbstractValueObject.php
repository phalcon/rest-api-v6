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

namespace Phalcon\Api\Domain\Infrastructure\DataSource;

use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\ValueObjectInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;

use function get_object_vars;

/**
 * @phpstan-import-type TCompany from CompanyTypes
 * @phpstan-import-type TUser from UserTypes
 */
abstract class AbstractValueObject implements ValueObjectInterface
{
    /**
     * @return TUser|TCompany
     */
    public function toArray(): array
    {
        /** @var TUser|TCompany $vars */
        $vars = get_object_vars($this);

        return $vars;
    }
}
