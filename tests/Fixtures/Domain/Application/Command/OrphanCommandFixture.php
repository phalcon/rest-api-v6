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

namespace Phalcon\Api\Tests\Fixtures\Domain\Application\Command;

use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;

/**
 * A command with no handler.
 *
 * The locator derives the handler by swapping "Command" for "Handler" in
 * the class name, so this one points at an OrphanHandlerFixture that does
 * not exist. CommandFixture cannot serve this purpose any more: the native
 * container autowires, so its handler resolves whether or not anything
 * registered it.
 */
final readonly class OrphanCommandFixture implements CommandInterface
{
    /**
     * @param string|null $name
     */
    public function __construct(
        public ?string $name,
    ) {
    }
}
