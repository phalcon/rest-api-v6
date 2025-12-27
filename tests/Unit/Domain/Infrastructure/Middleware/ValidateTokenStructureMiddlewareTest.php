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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Middleware;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\Encryption\JWTToken;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenStructureMiddleware;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Mvc\Micro;
use PHPUnit\Framework\Attributes\BackupGlobals;

#[BackupGlobals(true)]
final class ValidateTokenStructureMiddlewareTest extends AbstractUnitTestCase
{
    public function testValidateTokenStructureFailureBadSignature(): void
    {
        [$micro, $middleware] = $this->setupTest();

        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'HTTP_AUTHORIZATION' => 'Bearer abcd.abcd.abcd',
            'REQUEST_URI'        => '/user?id=1234',
        ];

        ob_start();
        $middleware->call($micro);
        $contents = ob_get_clean();

        $contents = json_decode($contents, true);
        $data     = $contents['data'];
        $errors   = $contents['errors'];

        $this->assertSame([], $data);

        $expected = [
            ['Invalid Header (not an array)'],
        ];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testValidateTokenStructureFailureNoDots(): void
    {
        [$micro, $middleware] = $this->setupTest();

        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'HTTP_AUTHORIZATION' => 'Bearer abcd.abcd',
            'REQUEST_URI'        => '/user?id=1234',
        ];

        ob_start();
        $middleware->call($micro);
        $contents = ob_get_clean();

        $contents = json_decode($contents, true);
        $data     = $contents['data'];
        $errors   = $contents['errors'];

        $this->assertSame([], $data);

        $expected = [
            ['Invalid JWT string (dots misalignment)'],
        ];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testValidateTokenStructureSuccess(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        $userData   = $this->getNewUserData();
        $domainUser = $userMapper->domain($userData);

        [$micro, $middleware] = $this->setupTest();
        /** @var JWTToken $jwtToken */
        $jwtToken = $micro->getSharedService(JWTToken::class);

        $token   = $jwtToken->getForUser($domainUser);
        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'REQUEST_URI'        => '/user?id=1234',
        ];

        ob_start();
        $actual = $middleware->call($micro);
        ob_end_flush();

        $this->assertTrue($actual);
    }

    /**
     * @return array
     */
    private function setupTest(): array
    {
        $micro      = new Micro($this->container);
        $middleware = $this->container->get(ValidateTokenStructureMiddleware::class);

        return [$micro, $middleware];
    }
}
