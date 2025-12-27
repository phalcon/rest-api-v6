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

use Phalcon\Api\Domain\Application\Auth\Command\AuthCommandFactory;
use Phalcon\Api\Domain\Application\Auth\Command\AuthLoginPostCommand;
use Phalcon\Api\Domain\Application\Auth\Command\AuthLogoutPostCommand;
use Phalcon\Api\Domain\Application\Auth\Command\AuthRefreshPostCommand;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Sanitizer\AuthSanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;

use function uniqid;

final class AuthCommandFactoryTest extends AbstractUnitTestCase
{
    public function testAuthenticate(): void
    {
        $sanitizer = $this->container->get(AuthSanitizer::class);
        $factory   = new AuthCommandFactory($sanitizer);

        $input = [
            'email'    => 'user@example.com',
            'password' => 's3cr3t',
        ];

        $command = $factory->authenticate($input);

        $this->assertInstanceOf(AuthLoginPostCommand::class, $command);

        $expected = 'user@example.com';
        $actual   = $command->email;
        $this->assertSame($expected, $actual);

        $expected = 's3cr3t';
        $actual   = $command->password;
        $this->assertSame($expected, $actual);
    }

    public function testLogout(): void
    {
        $sanitizer = $this->container->get(AuthSanitizer::class);
        $factory   = new AuthCommandFactory($sanitizer);

        $token = uniqid('token-');
        $input = [
            'token' => $token,
        ];

        $command = $factory->logout($input);

        $this->assertInstanceOf(AuthLogoutPostCommand::class, $command);

        $expected = $token;
        $actual   = $command->token;
        $this->assertSame($expected, $actual);
    }

    public function testRefresh(): void
    {
        $sanitizer = $this->container->get(AuthSanitizer::class);
        $factory   = new AuthCommandFactory($sanitizer);

        $token = uniqid('token-');
        $input = [
            'token' => $token,
        ];

        $command = $factory->refresh($input);

        $this->assertInstanceOf(AuthRefreshPostCommand::class, $command);

        $expected = $token;
        $actual   = $command->token;
        $this->assertSame($expected, $actual);
    }
}
