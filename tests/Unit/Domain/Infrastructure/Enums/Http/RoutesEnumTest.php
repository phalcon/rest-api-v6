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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Enums\Http;

use Phalcon\Api\Domain\Application\Auth\Service\AuthLoginPostService;
use Phalcon\Api\Domain\Application\Auth\Service\AuthLogoutPostService;
use Phalcon\Api\Domain\Application\Auth\Service\AuthRefreshPostService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyDeleteService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyGetManyService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyGetService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyPostService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyPutService;
use Phalcon\Api\Domain\Application\User\Service\UserDeleteService;
use Phalcon\Api\Domain\Application\User\Service\UserGetService;
use Phalcon\Api\Domain\Application\User\Service\UserPostService;
use Phalcon\Api\Domain\Application\User\Service\UserPutService;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\RoutesEnum;
use Phalcon\Api\Domain\Infrastructure\Middleware\HealthMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\NotFoundMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenClaimsMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenPresenceMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenRevokedMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenStructureMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenUserMiddleware;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class RoutesEnumTest extends AbstractUnitTestCase
{
    public static function getExamples(): array
    {
        return [
            [
                RoutesEnum::authLoginPost,
                '/auth',
                '/login',
                '/auth/login',
                RoutesEnum::POST,
                AuthLoginPostService::class,
            ],
            [
                RoutesEnum::authLogoutPost,
                '/auth',
                '/logout',
                '/auth/logout',
                RoutesEnum::POST,
                AuthLogoutPostService::class,
            ],
            [
                RoutesEnum::authRefreshPost,
                '/auth',
                '/refresh',
                '/auth/refresh',
                RoutesEnum::POST,
                AuthRefreshPostService::class,
            ],
            [
                RoutesEnum::companyDelete,
                '/company',
                '',
                '/company',
                RoutesEnum::DELETE,
                CompanyDeleteService::class,
            ],
            [
                RoutesEnum::companyGet,
                '/company',
                '',
                '/company',
                RoutesEnum::GET,
                CompanyGetService::class,
            ],
            [
                RoutesEnum::companyGetMany,
                '/company',
                '/all',
                '/company/all',
                RoutesEnum::GET,
                CompanyGetManyService::class,
            ],
            [
                RoutesEnum::companyPost,
                '/company',
                '',
                '/company',
                RoutesEnum::POST,
                CompanyPostService::class,
            ],
            [
                RoutesEnum::companyPut,
                '/company',
                '',
                '/company',
                RoutesEnum::PUT,
                CompanyPutService::class,
            ],
            [
                RoutesEnum::userDelete,
                '/user',
                '',
                '/user',
                RoutesEnum::DELETE,
                UserDeleteService::class,
            ],
            [
                RoutesEnum::userGet,
                '/user',
                '',
                '/user',
                RoutesEnum::GET,
                UserGetService::class,
            ],
            [
                RoutesEnum::userPost,
                '/user',
                '',
                '/user',
                RoutesEnum::POST,
                UserPostService::class,
            ],
            [
                RoutesEnum::userPut,
                '/user',
                '',
                '/user',
                RoutesEnum::PUT,
                UserPutService::class,
            ],
        ];
    }

    public function testCheckCount(): void
    {
        $expected = 12;
        $actual   = RoutesEnum::cases();
        $this->assertCount($expected, $actual);
    }

    #[DataProvider('getExamples')]
    public function testCheckItems(
        RoutesEnum $element,
        string $prefix,
        string $suffix,
        string $endpoint,
        string $method,
        string $service
    ) {
        $expected = $prefix;
        $actual   = $element->prefix();
        $this->assertSame($expected, $actual);

        $expected = $suffix;
        $actual   = $element->suffix();
        $this->assertSame($expected, $actual);

        $expected = $endpoint;
        $actual   = $element->endpoint();
        $this->assertSame($expected, $actual);

        $expected = $method;
        $actual   = $element->method();
        $this->assertSame($expected, $actual);

        $expected = $service;
        $actual   = $element->service();
        $this->assertSame($expected, $actual);
    }

    public function testMiddleware(): void
    {
        $expected = [
            NotFoundMiddleware::class               => RoutesEnum::EVENT_BEFORE,
            HealthMiddleware::class                 => RoutesEnum::EVENT_BEFORE,
            ValidateTokenPresenceMiddleware::class  => RoutesEnum::EVENT_BEFORE,
            ValidateTokenStructureMiddleware::class => RoutesEnum::EVENT_BEFORE,
            ValidateTokenUserMiddleware::class      => RoutesEnum::EVENT_BEFORE,
            ValidateTokenClaimsMiddleware::class    => RoutesEnum::EVENT_BEFORE,
            ValidateTokenRevokedMiddleware::class   => RoutesEnum::EVENT_BEFORE,
        ];
        $actual   = RoutesEnum::middleware();
        $this->assertSame($expected, $actual);
    }
}
