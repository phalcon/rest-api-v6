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

namespace Phalcon\Api\Tests\Fixtures\Domain\Application\Handler;

use Phalcon\ADR\Payload\Payload;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\CommandBus\HandlerInterface;
use Phalcon\Contracts\ADR\Payload\Payload as PayloadContract;

final readonly class HandlerFixture implements HandlerInterface
{
    public function __invoke(CommandInterface $command): PayloadContract
    {
        return Payload::success(['Name: ' . $command->name]);
    }
}
