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

use Phalcon\Api\Domain\Application\Auth\Command\AuthLoginPostCommand;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class AuthLoginPostCommandTest extends AbstractUnitTestCase
{
    public function testConstruct(): void
    {
        $email    = 'user@example.com';
        $password = 's3cr3t';

        $command = new AuthLoginPostCommand($email, $password);

        $expected = $email;
        $actual   = $command->email;
        $this->assertSame($expected, $actual);

        $expected = $password;
        $actual   = $command->password;
        $this->assertSame($expected, $actual);
    }

    public function testConstructEmpty(): void
    {
        $command = new AuthLoginPostCommand(null, null);

        $expected = null;
        $actual   = $command->email;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $command->password;
        $this->assertSame($expected, $actual);
    }
}
