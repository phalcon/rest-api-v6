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

namespace Phalcon\Api\Tests\Unit\Domain\Application\Company\Service;

use DateTimeImmutable;
use Phalcon\Api\Domain\Infrastructure\Constants\Cache as CacheConstants;
use Phalcon\Api\Domain\Infrastructure\Constants\Dates;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper\CompanyMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\RoutesEnum;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\CompaniesMigration;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;
use Phalcon\Cache\Cache;
use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

use function json_decode;
use function ob_get_clean;
use function ob_start;
use function restore_error_handler;
use function time;

#[BackupGlobals(true)]
final class CompanyServiceDispatchTest extends AbstractUnitTestCase
{
    #[RunInSeparateProcess]
    public function testDispatchGet(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);
        /** @var Cache $cache */
        $cache = $this->container->getShared(Cache::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var CompanyMapper $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);

        $companyMigration = new CompaniesMigration($this->getConnection());
        $userMigration    = new UsersMigration($this->getConnection());

        $dbUser        = $this->getNewUser($userMigration);
        $dbCompany     = $this->getNewCompany($companyMigration);
        $companyId     = $dbCompany['com_id'];
        $token         = $this->getUserToken($dbUser);
        $domainUser    = $userMapper->domain($dbUser);
        $domainCompany = $companyMapper->domain($dbCompany);

        /**
         * Store the token in the cache
         */
        $cacheKey = CacheConstants::getCacheTokenKey($domainUser, $token);
        /** @var int $expiration */
        $expiration     = $env->get(
            'TOKEN_EXPIRATION',
            CacheConstants::CACHE_TOKEN_EXPIRY,
            'int'
        );
        $expirationDate = (new DateTimeImmutable())
            ->modify('+' . $expiration . ' seconds')
            ->format(Dates::DATE_TIME_FORMAT)
        ;

        $payload = [
            'token'  => $token,
            'expiry' => $expirationDate,
        ];

        $cache->set($cacheKey, $payload, $expiration);

        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'REQUEST_URI'        => RoutesEnum::companyGet->endpoint(),
        ];

        $_GET = [
            'id' => $companyId,
        ];

        ob_start();
        require_once $env->appPath('public/index.php');
        $response = ob_get_clean();

        $contents = json_decode($response, true);

        restore_error_handler();

        $this->assertArrayHasKey('data', $contents);
        $this->assertArrayHasKey('errors', $contents);

        $data   = $contents['data'];
        $errors = $contents['errors'];

        $expected = [];
        $actual   = $errors;
        $this->assertSame($expected, $actual);

        $expected = [$companyId => $domainCompany->toArray()];
        $actual   = $data;
        $this->assertSame($expected, $actual);
    }
}
