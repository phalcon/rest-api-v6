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

use Phalcon\Api\Domain\Application\Auth\Command\AuthLogoutPostCommand;
use Phalcon\Api\Tests\AbstractUnitTestCase;

use function uniqid;

final class AuthLogoutPostCommandTest extends AbstractUnitTestCase
{
    public function testConstruct(): void
    {
        $token = uniqid('token-');

        $command = new AuthLogoutPostCommand($token);

        $expected = $token;
        $actual   = $command->token;
        $this->assertSame($expected, $actual);
    }

    public function testConstructEmpty(): void
    {
        $command = new AuthLogoutPostCommand(null);

        $expected = null;
        $actual   = $command->token;
        $this->assertSame($expected, $actual);
    }
}
