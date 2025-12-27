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

use DateTimeImmutable;
use InvalidArgumentException;
use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\Infrastructure\Constants\Cache as CacheConstants;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\Enums\Common\FlagsEnum;
use Phalcon\Api\Domain\Infrastructure\Enums\Common\JWTEnum;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Domain\Infrastructure\Exceptions\TokenValidationException;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Token\Parser;
use Phalcon\Encryption\Security\JWT\Token\Token;
use Phalcon\Encryption\Security\JWT\Validator;
use Phalcon\Support\Helper\Json\Decode;

/**
 * @phpstan-import-type TValidatorErrors from InputTypes
 *
 * Removed the final declaration so that this class can be mocked. This
 * class should not be extended
 */
class JWTToken
{
    /**
     * @var Parser|null
     */
    private ?Parser $parser = null;

    public function __construct(
        private readonly EnvManager $env
    ) {
    }

    /**
     * Returns the string token
     *
     * @param User $user
     *
     * @return string
     */
    public function getForUser(User $user): string
    {
        return $this->generateTokenForUser($user);
    }

    /**
     * Return the JWT Token object
     *
     * @param string $token
     *
     * @return Token
     */
    public function getObject(string $token): Token
    {
        try {
            if (null === $this->parser) {
                $this->parser = new Parser(new Decode());
            }

            $tokenObject = $this->parser->parse($token);
        } catch (InvalidArgumentException $ex) {
            throw TokenValidationException::new($ex->getMessage());
        }

        return $tokenObject;
    }

    /**
     * Returns the string token
     *
     * @param User $user
     *
     * @return string
     */
    public function getRefreshForUser(User $user): string
    {
        return $this->generateTokenForUser($user, true);
    }

    /**
     * @param UserRepositoryInterface $repository
     * @param Token                   $token
     *
     * @return User|null
     */
    public function getUser(
        UserRepositoryInterface $repository,
        Token $token,
    ): ?User {
        /** @var string $issuer */
        $issuer = $token->getClaims()->get(JWTEnum::Issuer->value);
        /** @var string $tokenId */
        $tokenId = $token->getClaims()->get(JWTEnum::Id->value);
        /** @var string $userId */
        $userId = $token->getClaims()->get(JWTEnum::UserId->value);

        $criteria = [
            'usr_id'          => $userId,
            'usr_status_flag' => FlagsEnum::Active->value,
            'usr_issuer'      => $issuer,
            'usr_token_id'    => $tokenId,
        ];

        return $repository->findOneBy($criteria);
    }

    /**
     * Returns an array with the validation errors for this token
     *
     * @param Token $tokenObject
     * @param User  $user
     *
     * @return TValidatorErrors
     */
    public function validate(
        Token $tokenObject,
        User $user
    ): array {
        $validator = new Validator($tokenObject);
        $signer    = new Hmac();
        $now       = new DateTimeImmutable();

        /** @var string $tokenId */
        $tokenId = $user->tokenId;
        /** @var string $issuer */
        $issuer = $user->issuer;
        /** @var string $tokenPassword */
        $tokenPassword = $user->tokenPassword;
        /** @var int $userId */
        $userId = $user->id;

        $validator
            ->validateId($tokenId)
            ->validateAudience($this->getTokenAudience())
            ->validateIssuer($issuer)
            ->validateNotBefore($now->getTimestamp())
            ->validateIssuedAt($now->getTimestamp())
            ->validateExpiration($now->getTimestamp())
            ->validateSignature($signer, $tokenPassword)
            ->validateClaim(JWTEnum::UserId->value, $userId)
        ;

        /** @var TValidatorErrors $errors */
        $errors = $validator->getErrors();

        return $errors;
    }

    /**
     * Returns the string token
     *
     * @param User $user
     * @param bool $isRefresh
     *
     * @return string
     */
    private function generateTokenForUser(
        User $user,
        bool $isRefresh = false
    ): string {
        /** @var int $expiration */
        $expiration = $this->env->get(
            'TOKEN_EXPIRATION',
            CacheConstants::CACHE_TOKEN_EXPIRY,
            'int'
        );

        $now       = new DateTimeImmutable();
        $expiresAt = $now->modify('+' . $expiration . ' seconds');
        /**
         * This is to ensure that the token is valid the minute we issue it
         */
        $now = $now->modify('-1 second');

        $tokenBuilder = new Builder(new Hmac());
        /** @var string $issuer */
        $issuer = $user->issuer;
        /** @var string $tokenPassword */
        $tokenPassword = $user->tokenPassword;
        /** @var string $tokenId */
        $tokenId = $user->tokenId;
        /** @var int $userId */
        $userId = $user->id;

        $tokenObject = $tokenBuilder
            ->setIssuer($issuer)
            ->setAudience($this->getTokenAudience())
            ->setId($tokenId)
            ->setNotBefore($now->getTimestamp())
            ->setIssuedAt($now->getTimestamp())
            ->setExpirationTime($expiresAt->getTimestamp())
            ->setPassphrase($tokenPassword)
            ->addClaim(JWTEnum::UserId->value, $userId)
            ->addClaim(JWTEnum::Refresh->value, $isRefresh)
            ->getToken()
        ;

        return $tokenObject->getToken();
    }


    /**
     * Returns the default audience for the tokens
     *
     * @return string
     */
    private function getTokenAudience(): string
    {
        /** @var string $audience */
        $audience = $this->env->get(
            'TOKEN_AUDIENCE',
            'https://rest-api.phalcon.io'
        );

        return $audience;
    }
}
