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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Validator;

use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\ValidatorEnumInterface;
use Phalcon\Filter\Validation\Validator\PresenceOf;

enum AuthTokenValidatorEnum implements ValidatorEnumInterface
{
    case token;

    public function allowEmpty(): bool
    {
        return false;
    }

    public function validators(): array
    {
        return [PresenceOf::class];
    }
}
