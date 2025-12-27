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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Auth\Validator;

use Exception;
use Phalcon\Api\Domain\Application\Auth\Command\AuthCommandFactory;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Sanitizer\AuthSanitizer;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Validator\AuthTokenValidator;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepository;
use Phalcon\Api\Domain\Infrastructure\Encryption\JWTToken;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenCache;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenCacheInterface;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManager;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Validation;

final class AuthTokenValidatorTest extends AbstractUnitTestCase
{
    public function testFailureTokenNotPresent(): void
    {
        /** @var AuthTokenValidator $validator */
        $validator = $this->container->get(AuthTokenValidator::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);

        $input       = [];
        $authCommand = $factory->logout($input);

        $result = $validator->validate($authCommand);
        $actual = $result->getErrors();

        $expected = [
            HttpCodesEnum::AppTokenNotPresent->error(),
        ];

        $this->assertSame($expected, $actual);
    }

    public function testFailureTokenNotValid(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->get(EnvManager::class);
        /** @var TokenCacheInterface $tokenCache */
        $tokenCache = $this->container->get(TokenCache::class);
        /** @var AuthSanitizer $sanitizer */
        $sanitizer = $this->container->get(AuthSanitizer::class);
        /** @var UserRepository $repository */
        $repository = $this->container->get(UserRepository::class);
        /** @var Validation $validator */
        $validation = $this->container->get(Validation::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);

        $mockJWTToken = $this
            ->getMockBuilder(JWTToken::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getObject',
                ]
            )
            ->getMock()
        ;
        $mockJWTToken
            ->method('getObject')
            ->willThrowException(new Exception('error'))
        ;

        $userData           = $this->getNewUserData();
        $userData['usr_id'] = rand(1, 100);
        $token              = $this->getUserToken($userData);

        $tokenManager = new TokenManager($tokenCache, $env, $mockJWTToken);

        $input       = [
            'token' => $token,
        ];
        $authCommand = $factory->logout($input);

        $validator = new AuthTokenValidator($tokenManager, $repository, $validation);
        $result    = $validator->validate($authCommand);
        $actual    = $result->getErrors();

        $expected = [
            HttpCodesEnum::AppTokenNotValid->error(),
        ];

        $this->assertSame($expected, $actual);
    }
}
