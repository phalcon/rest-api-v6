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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Encryption;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepository;
use Phalcon\Api\Domain\Infrastructure\Encryption\JWTToken;
use Phalcon\Api\Domain\Infrastructure\Exceptions\TokenValidationException;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Encryption\Security\JWT\Token\Token;

final class JWTTokenTest extends AbstractUnitTestCase
{
    private JWTToken $jwtToken;

    public function setUp(): void
    {
        parent::setUp();

        $this->jwtToken = $this->container->get(JWTToken::class);
    }

    public function testGetForUserReturnsTokenString(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        $userData   = $this->getUserData();
        $domainUser = $userMapper->domain($userData);

        $token = $this->jwtToken->getForUser($domainUser);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testGetObjectReturnsPlainToken(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        $userData   = $this->getUserData();
        $domainUser = $userMapper->domain($userData);

        $tokenString = $this->jwtToken->getForUser($domainUser);

        $plain = $this->jwtToken->getObject($tokenString);
        $this->assertInstanceOf(Token::class, $plain);
    }

    public function testGetObjectThrowsOnCannotDecodeContent(): void
    {
        $this->expectException(TokenValidationException::class);
        $this->expectExceptionMessage('Invalid Header (not an array)');

        // Simulate exception by calling with invalid token
        $this->jwtToken->getObject('invalid.token.content');
    }

    public function testGetObjectThrowsOnInvalidTokenStructure(): void
    {
        $this->expectException(TokenValidationException::class);

        // This will throw, as the structure is invalid
        $this->jwtToken->getObject('abc.def.ghi');
    }

    public function testGetUserReturnsUserArray(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        $userData   = $this->getUserData();
        $domainUser = $userMapper->domain($userData);

        $tokenString = $this->jwtToken->getForUser($domainUser);
        $plain       = $this->jwtToken->getObject($tokenString);

        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'findOneBy',
                ]
            )
            ->getMock()
        ;

        $userRepository->expects($this->once())
                       ->method('findOneBy')
                       ->willReturn($domainUser)
        ;

        $result = $this->jwtToken->getUser($userRepository, $plain);
        $this->assertEquals($domainUser, $result);
    }

    public function testValidateSuccess(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        $userData   = $this->getUserData();
        $domainUser = $userMapper->domain($userData);

        $tokenString = $this->jwtToken->getForUser($domainUser);
        $plain       = $this->jwtToken->getObject($tokenString);

        $actual = $this->jwtToken->validate($plain, $domainUser);

        $this->assertSame([], $actual);
    }

    /**
     * @return array
     */
    private function getUserData(): array
    {
        $user                       = $this->getNewUserData();
        $user['usr_id']             = 2;
        $user['usr_token_id']       = $this->getStrongPassword();
        $user['usr_issuer']         = 'issuer';
        $user['usr_token_password'] = $this->getStrongPassword();

        return $user;
    }
}
