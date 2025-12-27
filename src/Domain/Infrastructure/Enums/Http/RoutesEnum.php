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

namespace Phalcon\Api\Domain\Infrastructure\Enums\Http;

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
use Phalcon\Api\Domain\Infrastructure\Middleware\HealthMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\NotFoundMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenClaimsMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenPresenceMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenRevokedMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenStructureMiddleware;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenUserMiddleware;

use function str_replace;

/**
 * @phpstan-type TMiddleware array<string, 'before'|'finish'>
 */
enum RoutesEnum: int
{
    /**
     * Methods
     */
    public const DELETE = 'delete';
    /**
     * Events
     */
    public const EVENT_BEFORE = 'before';
    public const EVENT_FINISH = 'finish';
    public const GET          = 'get';
    public const POST         = 'post';
    public const PUT          = 'put';

    case authLoginPost   = 11;
    case authLogoutPost  = 12;
    case authRefreshPost = 13;

    case companyDelete  = 31;
    case companyGet     = 32;
    case companyGetMany = 33;
    case companyPost    = 34;
    case companyPut     = 35;

    case userDelete = 21;
    case userGet    = 22;
    case userPost   = 23;
    case userPut    = 24;

    /**
     * @return string
     */
    public function endpoint(): string
    {
        return $this->prefix() . $this->suffix();
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return match ($this) {
            self::authLoginPost,
            self::authLogoutPost,
            self::authRefreshPost,
            self::companyPost,
            self::userPost   => self::POST,
            self::companyDelete,
            self::userDelete => self::DELETE,
            self::companyGet,
            self::companyGetMany,
            self::userGet    => self::GET,
            self::companyPut,
            self::userPut    => self::PUT,
        };
    }

    /**
     * @return TMiddleware
     */
    public static function middleware(): array
    {
        return [
            NotFoundMiddleware::class               => self::EVENT_BEFORE,
            HealthMiddleware::class                 => self::EVENT_BEFORE,
            ValidateTokenPresenceMiddleware::class  => self::EVENT_BEFORE,
            ValidateTokenStructureMiddleware::class => self::EVENT_BEFORE,
            ValidateTokenUserMiddleware::class      => self::EVENT_BEFORE,
            ValidateTokenClaimsMiddleware::class    => self::EVENT_BEFORE,
            ValidateTokenRevokedMiddleware::class   => self::EVENT_BEFORE,
        ];
    }

    /**
     * @return string
     */
    public function prefix(): string
    {
        $endpoint = match ($this) {
            self::authLoginPost,
            self::authLogoutPost,
            self::authRefreshPost => 'auth',
            self::companyDelete,
            self::companyGet,
            self::companyGetMany,
            self::companyPost,
            self::companyPut      => 'company',
            self::userDelete,
            self::userGet,
            self::userPost,
            self::userPut         => 'user',
        };

        return '/' . str_replace('-', '/', $endpoint);
    }

    public function service(): string
    {
        return match ($this) {
            self::authLoginPost   => AuthLoginPostService::class,
            self::authLogoutPost  => AuthLogoutPostService::class,
            self::authRefreshPost => AuthRefreshPostService::class,
            self::companyDelete   => CompanyDeleteService::class,
            self::companyGet      => CompanyGetService::class,
            self::companyGetMany  => CompanyGetManyService::class,
            self::companyPost     => CompanyPostService::class,
            self::companyPut      => CompanyPutService::class,
            self::userDelete      => UserDeleteService::class,
            self::userGet         => UserGetService::class,
            self::userPost        => UserPostService::class,
            self::userPut         => UserPutService::class,
        };
    }

    /**
     * @return string
     */
    public function suffix(): string
    {
        return match ($this) {
            self::authLoginPost   => '/login',
            self::authLogoutPost  => '/logout',
            self::authRefreshPost => '/refresh',
            self::companyGetMany  => '/all',
            default               => '',
        };
    }
}
