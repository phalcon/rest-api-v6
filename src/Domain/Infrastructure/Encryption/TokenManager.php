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

namespace Phalcon\Api\Domain\Infrastructure\Encryption;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Encryption\Security\JWT\Token\Token;
use Throwable;

/**
 * Small component to issue/rotate/revoke tokens and
 * interact with cache.
 *
 * @phpstan-import-type TTokenIssue from TokenManagerInterface
 * @phpstan-import-type TValidatorErrors from InputTypes
 */
final class TokenManager implements TokenManagerInterface
{
    private string $errorMessage = '';

    /**
     * @param TokenCacheInterface $tokenCache
     * @param EnvManager          $env
     * @param JWTToken            $jwtToken
     */
    public function __construct(
        private readonly TokenCacheInterface $tokenCache,
        private readonly EnvManager $env,
        private readonly JWTToken $jwtToken,
    ) {
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * Parse a token and return either null or a Token object
     *
     * @param string|null $token
     *
     * @return Token|null
     */
    public function getObject(?string $token): ?Token
    {
        if (true === empty($token)) {
            return null;
        }

        try {
            $this->errorMessage = '';
            return $this->jwtToken->getObject($token);
        } catch (Throwable $ex) {
            $this->errorMessage = $ex->getMessage();
            return null;
        }
    }

    /**
     * Return the domain user from the database
     *
     * @param UserRepositoryInterface $repository
     * @param Token                   $tokenObject
     *
     * @return User|null
     */
    public function getUser(
        UserRepositoryInterface $repository,
        Token $tokenObject
    ): ?User {
        return $this->jwtToken->getUser($repository, $tokenObject);
    }

    /**
     * Issue new tokens for the user (token, refreshToken)
     *
     * @param User $domainUser
     *
     * @return TTokenIssue
     */
    public function issue(User $domainUser): array
    {
        $token   = $this->jwtToken->getForUser($domainUser);
        $refresh = $this->jwtToken->getRefreshForUser($domainUser);

        $this->tokenCache->storeTokenInCache($this->env, $domainUser, $token);
        $this->tokenCache->storeTokenInCache($this->env, $domainUser, $refresh);

        return [
            'token'        => $token,
            'refreshToken' => $refresh,
        ];
    }

    /**
     * Revoke old tokens and issue new ones.
     *
     * @param User $domainUser
     *
     * @return TTokenIssue
     */
    public function refresh(User $domainUser): array
    {
        $this->revoke($domainUser);

        return $this->issue($domainUser);
    }

    /**
     * Revoke cached tokens for a user.
     *
     * @param User $domainUser
     *
     * @return void
     */
    public function revoke(User $domainUser): void
    {
        $this->tokenCache->invalidateForUser($this->env, $domainUser);
    }

    /**
     * Validate token claims
     *
     * @param Token $tokenObject
     * @param User  $user
     *
     * @return TValidatorErrors
     */
    public function validate(Token $tokenObject, User $user): array
    {
        return $this->jwtToken->validate($tokenObject, $user);
    }
}
