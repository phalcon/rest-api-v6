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

namespace Phalcon\Api\Domain\Infrastructure\Enums\Common;

use Phalcon\Api\Domain\Infrastructure\Enums\EnumsInterface;

enum FlagsEnum: int implements EnumsInterface
{
    case Active   = 1;
    case Disabled = 2;
    case Inactive = 3;

    /**
     * Return the text associated with the option
     *
     * @return string
     */
    public function text(): string
    {
        return $this->name;
    }
}
