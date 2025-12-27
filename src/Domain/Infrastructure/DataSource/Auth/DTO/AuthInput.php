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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Auth\DTO;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\AbstractInputValueObject;

/**
 * @phpstan-import-type TAuthInput from InputTypes
 */
final class AuthInput extends AbstractInputValueObject
{
    /**
     * @param string|null $email
     * @param string|null $password
     * @param string|null $token
     */
    public function __construct(
        public readonly ?string $email,
        public readonly ?string $password,
        public readonly ?string $token
    ) {
    }

    /**
     * Build the concrete DTO from a sanitized array.
     *
     * @param TAuthInput $sanitized
     *
     * @return static
     */
    protected static function fromArray(array $sanitized): static
    {
        $email    = $sanitized['email'] ?? null;
        $password = $sanitized['password'] ?? null;
        $token    = $sanitized['token'] ?? null;

        return new self($email, $password, $token);
    }
}
