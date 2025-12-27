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

namespace Phalcon\Api\Domain\Application\Auth\Command;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\DTO\AuthInput;
use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\SanitizerInterface;

/**
 * @phpstan-import-type TAuthLoginInput from InputTypes
 * @phpstan-import-type TAuthLogoutInput from InputTypes
 * @phpstan-import-type TAuthRefreshInput from InputTypes
 */
final readonly class AuthCommandFactory implements AuthCommandFactoryInterface
{
    /**
     * @param SanitizerInterface $sanitizer
     */
    public function __construct(
        private SanitizerInterface $sanitizer,
    ) {
    }

    /**
     * @param TAuthLoginInput $input
     *
     * @return AuthLoginPostCommand
     */
    public function authenticate(array $input): AuthLoginPostCommand
    {
        /**
         * Sanitize the input
         */
        $dto = AuthInput::new($this->sanitizer, $input);

        /**
         * Return the command back
         */
        return new AuthLoginPostCommand($dto->email, $dto->password);
    }

    /**
     * @param TAuthLogoutInput $input
     *
     * @return AuthLogoutPostCommand
     */
    public function logout(array $input): AuthLogoutPostCommand
    {
        /**
         * Sanitize the input
         */
        $dto = AuthInput::new($this->sanitizer, $input);

        /**
         * Return the command back
         */
        return new AuthLogoutPostCommand($dto->token);
    }

    /**
     * @param TAuthRefreshInput $input
     *
     * @return AuthRefreshPostCommand
     */
    public function refresh(array $input): AuthRefreshPostCommand
    {
        /**
         * Sanitize the input
         */
        $dto = AuthInput::new($this->sanitizer, $input);

        /**
         * Return the command back
         */
        return new AuthRefreshPostCommand($dto->token);
    }
}
