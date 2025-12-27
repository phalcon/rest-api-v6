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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Interface;

use Phalcon\Api\Domain\ADR\InputTypes;

/**
 * @phpstan-import-type TInputSanitize from InputTypes
 */
interface SanitizerInterface
{
    /**
     * Return a sanitized array of the input
     *
     * @param TInputSanitize $input
     *
     * @return TInputSanitize
     */
    public function sanitize(array $input): array;
}
