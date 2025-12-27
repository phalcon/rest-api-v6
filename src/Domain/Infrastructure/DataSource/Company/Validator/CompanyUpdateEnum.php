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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Company\Validator;

use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\ValidatorEnumInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\AbsInt;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;

enum CompanyUpdateEnum implements ValidatorEnumInterface
{
    case id;
    case email;

    public function allowEmpty(): bool
    {
        return match ($this) {
            self::id => false,
            default  => true,
        };
    }

    public function validators(): array
    {
        return match ($this) {
            self::id    => [
                PresenceOf::class,
                AbsInt::class,
            ],
            self::email => [
                Email::class,
            ],
        };
    }
}
