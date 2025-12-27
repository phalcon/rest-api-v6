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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Transformer;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;

/**
 * Transforms the data for the payload
 *
 * @phpstan-type TTokens = array{
 *     token: string,
 *     refreshToken: string
 * }
 * @phpstan-import-type TLoginResponsePayload from UserTypes
 */
final class AuthTransformer
{
    /**
     * @param User    $domainUser
     * @param TTokens $tokens
     *
     * @return TLoginResponsePayload
     */
    public function login(User $domainUser, array $tokens): array
    {
        return [
            'authenticated' => true,
            'user'          => [
                'id'    => $domainUser->id,
                'name'  => $domainUser->fullName(),
                'email' => $domainUser->email,
            ],
            'jwt'           => [
                'token'        => $tokens['token'],
                'refreshToken' => $tokens['refreshToken'],
            ],
        ];
    }

    /**
     * @return array{authenticated: false}
     */
    public function logout(): array
    {
        return ['authenticated' => false];
    }

    /**
     * @param TTokens $tokens
     *
     * @return TTokens
     */
    public function refresh(array $tokens): array
    {
        return [
            'token'        => $tokens['token'],
            'refreshToken' => $tokens['refreshToken'],
        ];
    }
}
