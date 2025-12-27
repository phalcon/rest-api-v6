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

namespace Phalcon\Api\Domain\Application\Auth\Handler;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Application\Auth\Command\AuthLoginPostCommand;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\CommandBus\HandlerInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Transformer\AuthTransformer;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\ValidatorInterface;
use Phalcon\Api\Domain\Infrastructure\Encryption\Security;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;

/**
 * @phpstan-import-type TAuthLoginInput from InputTypes
 */
final readonly class AuthLoginPostHandler implements HandlerInterface
{
    /**
     * @param UserRepositoryInterface $repository
     * @param TokenManagerInterface   $tokenManager
     * @param AuthTransformer         $transformer
     * @param Security                $security
     * @param ValidatorInterface      $validator
     */
    public function __construct(
        private UserRepositoryInterface $repository,
        private TokenManagerInterface $tokenManager,
        private AuthTransformer $transformer,
        private Security $security,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * Authenticates users (login)
     *
     * @param CommandInterface $command
     *
     * @return Payload
     */
    public function __invoke(CommandInterface $command): Payload
    {
        /**
         * Validate
         */
        /** @var AuthLoginPostCommand $command */
        $validation = $this->validator->validate($command);
        if (!$validation->isValid()) {
            return Payload::unauthorized($validation->getErrors());
        }

        /**
         * Find the user by email
         */
        /** @var string $email */
        $email      = $command->email;
        $domainUser = $this->repository->findByEmail($email);
        if (null === $domainUser) {
            return Payload::unauthorized([HttpCodesEnum::AppIncorrectCredentials->error()]);
        }

        /**
         * Verify the password
         */
        /** @var string $suppliedPassword */
        $suppliedPassword = $command->password;
        /** @var string $dbPassword */
        $dbPassword = $domainUser->password;
        if (true !== $this->security->verify($suppliedPassword, $dbPassword)) {
            return Payload::unauthorized([HttpCodesEnum::AppIncorrectCredentials->error()]);
        }

        /**
         * Issue a new set of tokens
         */
        $tokens = $this->tokenManager->issue($domainUser);

        return Payload::success($this->transformer->login($domainUser, $tokens));
    }
}
