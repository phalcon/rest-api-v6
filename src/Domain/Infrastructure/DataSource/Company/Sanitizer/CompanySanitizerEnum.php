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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Company\Sanitizer;

use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\SanitizerEnumInterface;
use Phalcon\Filter\Filter;

enum CompanySanitizerEnum implements SanitizerEnumInterface
{
    case id;
    case name;
    case phone;
    case email;
    case website;
    case addressLine1;
    case addressLine2;
    case city;
    case stateProvince;
    case zipCode;
    case country;
    case createdDate;
    case createdUserId;
    case updatedDate;
    case updatedUserId;
    case page;
    case perPage;

    public function default(): mixed
    {
        return match ($this) {
            self::id,
            self::createdUserId,
            self::updatedUserId => 0,
            self::page          => 1,
            self::perPage       => 10,
            default             => null
        };
    }

    public function sanitizer(): string
    {
        return match ($this) {
            self::id,
            self::createdUserId,
            self::updatedUserId,
            self::page,
            self::perPage => Filter::FILTER_ABSINT,
            self::email   => Filter::FILTER_EMAIL,
            default       => Filter::FILTER_STRIPTAGS
        };
    }
}
