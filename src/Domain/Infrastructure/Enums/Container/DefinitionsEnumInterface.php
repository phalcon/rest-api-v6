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

namespace Phalcon\Api\Domain\Infrastructure\Enums\Container;

use BackedEnum;
use Phalcon\Api\Domain\Infrastructure\Container;

/**
 * @phpstan-import-type TService from Container
 */
interface DefinitionsEnumInterface extends BackedEnum
{
    /**
     * @return TService
     */
    public function definition(): array;

    /**
     * @return bool
     */
    public function isShared(): bool;
}
