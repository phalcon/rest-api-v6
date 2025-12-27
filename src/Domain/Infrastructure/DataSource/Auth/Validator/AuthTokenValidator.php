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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Validator;

use Phalcon\Api\Domain\Application\Auth\Command\AuthLogoutPostCommand;
use Phalcon\Api\Domain\Application\Auth\Command\AuthRefreshPostCommand;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\AbstractValidator;
use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\Result;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Domain\Infrastructure\Enums\Common\JWTEnum;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Encryption\Security\JWT\Token\Token;
use Phalcon\Filter\Validation\ValidationInterface;

final class AuthTokenValidator extends AbstractValidator
{
    protected string $fields = AuthTokenValidatorEnum::class;

    public function __construct(
        private readonly TokenManagerInterface $tokenManager,
        private readonly UserRepositoryInterface $userRepository,
        ValidationInterface $validator,
    ) {
        parent::__construct($validator);
    }

    /**
     * Validate a AuthInput and return an array of errors.
     * Empty array means valid.
     *
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function validate(CommandInterface $command): Result
    {
        $errors = $this->runValidations($command);
        if (true !== empty($errors)) {
            return Result::error([HttpCodesEnum::AppTokenNotPresent->error()]);
        }

        /** @var AuthLogoutPostCommand|AuthRefreshPostCommand $command */
        /** @var string $token */
        $token       = $command->token;
        $tokenObject = $this->tokenManager->getObject($token);
        if (null === $tokenObject) {
            return Result::error([HttpCodesEnum::AppTokenNotValid->error()]);
        }

        if ($this->tokenIsNotRefresh($tokenObject)) {
            return Result::error([HttpCodesEnum::AppTokenNotValid->error()]);
        }

        $domainUser = $this->tokenManager->getUser($this->userRepository, $tokenObject);
        if (null === $domainUser) {
            return Result::error([HttpCodesEnum::AppTokenInvalidUser->error()]);
        }

        $errors = $this->tokenManager->validate($tokenObject, $domainUser);
        if (!empty($errors)) {
            return Result::error($errors);
        }

        $result = Result::success();
        $result->setMeta('user', $domainUser);

        return $result;
    }

    /**
     * Return if the token is a refresh one or not
     *
     * @param Token $tokenObject
     *
     * @return bool
     */
    private function tokenIsNotRefresh(Token $tokenObject): bool
    {
        $isRefresh = $tokenObject->getClaims()->get(JWTEnum::Refresh->value);

        return false === $isRefresh;
    }
}
