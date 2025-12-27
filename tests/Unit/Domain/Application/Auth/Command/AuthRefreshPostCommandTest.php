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

namespace Phalcon\Api\Tests\Unit\Domain\Application\Auth\Command;

use Phalcon\Api\Domain\Application\Auth\Command\AuthRefreshPostCommand;
use Phalcon\Api\Tests\AbstractUnitTestCase;

use function uniqid;

final class AuthRefreshPostCommandTest extends AbstractUnitTestCase
{
    public function testConstruct(): void
    {
        $token = uniqid('token-');

        $command = new AuthRefreshPostCommand($token);

        $expected = $token;
        $actual   = $command->token;
        $this->assertSame($expected, $actual);
    }

    public function testConstructEmpty(): void
    {
        $command = new AuthRefreshPostCommand(null);

        $expected = null;
        $actual   = $command->token;
        $this->assertSame($expected, $actual);
    }
}
