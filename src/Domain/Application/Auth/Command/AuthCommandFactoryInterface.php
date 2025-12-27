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

/**
 * @phpstan-import-type TAuthLoginInput from InputTypes
 * @phpstan-import-type TAuthLogoutInput from InputTypes
 * @phpstan-import-type TAuthRefreshInput from InputTypes
 */
interface AuthCommandFactoryInterface
{
    /**
     * @param TAuthLoginInput $input
     *
     * @return AuthLoginPostCommand
     */
    public function authenticate(array $input): AuthLoginPostCommand;

    /**
     * @param TAuthLogoutInput $input
     *
     * @return AuthLogoutPostCommand
     */
    public function logout(array $input): AuthLogoutPostCommand;

    /**
     * @param TAuthRefreshInput $input
     *
     * @return AuthRefreshPostCommand
     */
    public function refresh(array $input): AuthRefreshPostCommand;
}
