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

namespace Phalcon\Api\Domain\Infrastructure\Constants;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

final class Dates
{
    public const DATE_FORMAT          = 'Y-m-d';
    public const DATE_NOW             = 'NOW()';
    public const DATE_TIME_FORMAT     = 'Y-m-d H:i:s';
    public const DATE_TIME_UTC_FORMAT = 'Y-m-d\\TH:i:sp';
    public const DATE_TIME_ZONE       = 'UTC';

    /**
     * @param string $date
     * @param string $format
     *
     * @return string
     * @throws Exception
     */
    public static function toUTC(
        string $date = 'now',
        string $format = self::DATE_TIME_UTC_FORMAT
    ): string {
        return (new DateTimeImmutable(
            $date,
            new DateTimeZone(self::DATE_TIME_ZONE)
        ))->format($format);
    }
}
