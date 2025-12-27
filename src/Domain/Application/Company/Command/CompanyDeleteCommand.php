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

final readonly class CompanyDeleteCommand implements CommandInterface
{
    /**
     * @param int $id
     */
    public function __construct(
        public int $id,
    ) {
    }
}
