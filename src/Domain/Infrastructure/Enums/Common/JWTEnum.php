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

namespace Phalcon\Api\Domain\Infrastructure\Enums\Common;

enum JWTEnum: string
{
    /**
     * Headers
     */
    case Type        = 'typ';
    case Algo        = 'alg';
    case ContentType = 'cty';

    /**
     * Claims
     */
    case Audience       = 'aud';
    case ExpirationTime = 'exp';
    case Id             = 'jti';
    case IssuedAt       = 'iat';
    case Issuer         = 'iss';
    case NotBefore      = 'nbf';
    case Subject        = 'sub';

    /**
     * Custom claims
     */
    case UserId  = 'uid';
    case Refresh = 'rid';
}
