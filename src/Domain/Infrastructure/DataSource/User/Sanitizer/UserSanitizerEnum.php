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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\User\Sanitizer;

use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\SanitizerEnumInterface;
use Phalcon\Filter\Filter;

enum UserSanitizerEnum implements SanitizerEnumInterface
{
    case id;
    case status;
    case email;
    case password;
    case namePrefix;
    case nameFirst;
    case nameLast;
    case nameMiddle;
    case nameSuffix;
    case issuer;
    case tokenPassword;
    case tokenId;
    case preferences;
    case createdDate;
    case createdUserId;
    case updatedDate;
    case updatedUserId;

    public function default(): mixed
    {
        return match ($this) {
            self::id,
            self::status,
            self::createdUserId,
            self::updatedUserId => 0,
            default             => null
        };
    }

    public function sanitizer(): string
    {
        return match ($this) {
            self::id,
            self::status,
            self::createdUserId,
            self::updatedUserId => Filter::FILTER_ABSINT,
            self::email         => Filter::FILTER_EMAIL,
            self::password,
            self::tokenId,
            self::issuer,
            self::tokenPassword => '', // Password will be distorted
            default             => Filter::FILTER_STRIPTAGS
        };
    }
}
