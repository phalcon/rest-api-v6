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

use UnitEnum;

interface ValidatorEnumInterface extends UnitEnum
{
    /**
     * @return bool
     */
    public function allowEmpty(): bool;

    /**
     * @return array<array-key, class-string>
     */
    public function validators(): array;
}
