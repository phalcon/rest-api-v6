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

use function password_hash;
use function password_verify;

use const PASSWORD_ARGON2I;

/**
 * NOTE: if the algorithm changes (for `password_hash`) it will invalidate
 * all passwords in the database.
 */
final class Security
{
    private const ALGO = PASSWORD_ARGON2I;

    /**
     * Creates a password hash using password_hash
     *
     * @param string $password
     *
     * @return string
     */
    public function hash(string $password): string
    {
        return password_hash($password, self::ALGO);
    }

    /**
     * Checks a plain text password and its hash version to see if they match
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
