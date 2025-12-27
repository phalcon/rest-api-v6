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

use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\AbstractValidator;

final class CompanyValidator extends AbstractValidator
{
    protected string $fields = CompanyInsertEnum::class;
}
