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
use Phalcon\Encryption\Security\JWT\Token\Token;

/**
 * @phpstan-type TTokenIssue array{
 *      token: string,
 *      refreshToken: string
 * }
 *
 * @phpstan-import-type TValidatorErrors from InputTypes
 */
interface TokenManagerInterface
{
    /**
     * Returns the last error message
     *
     * @return string
     */
    public function getErrorMessage(): string;

    /**
     * Parse a token and return either null or a Token object
     *
     * @param string $token
     *
     * @return Token|null
     */
    public function getObject(string $token): ?Token;

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
    ): ?User;

    /**
     * Issue new tokens for the user (token, refreshToken)
     *
     * @param User $domainUser
     *
     * @return TTokenIssue
     */
    public function issue(User $domainUser): array;

    /**
     * Revoke old tokens and issue new ones.
     *
     * @param User $domainUser
     *
     * @return TTokenIssue
     */
    public function refresh(User $domainUser): array;

    /**
     * Revoke cached tokens for a user.
     *
     * @param User $domainUser
     *
     * @return void
     */
    public function revoke(User $domainUser): void;

    /**
     * Validate token claims
     *
     * @param Token $tokenObject
     * @param User  $user
     *
     * @return TValidatorErrors
     */
    public function validate(Token $tokenObject, User $user): array;
}
