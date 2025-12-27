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

namespace Phalcon\Api\Domain\Infrastructure\Enums;

use BackedEnum;

interface EnumsInterface extends BackedEnum
{
    /**
     * Return the text associated with the option
     *
     * @return string
     */
    public function text(): string;
}
