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

namespace Phalcon\Api\Domain\Infrastructure\DataSource;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\SanitizerEnumInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\SanitizerInterface;
use Phalcon\Filter\FilterInterface;

/**
 * @phpstan-import-type TInputSanitize from InputTypes
 */
abstract class AbstractSanitizer implements SanitizerInterface
{
    protected string $enum = '';

    public function __construct(
        private readonly FilterInterface $filter,
    ) {
    }

    /**
     * @param TInputSanitize $input
     *
     * @return TInputSanitize
     */
    public function sanitize(array $input): array
    {
        $enum = $this->enum;
        /** @var SanitizerEnumInterface[] $fields */
        $fields = $enum::cases();

        /**
         * Set defaults
         */
        $sanitized = [];

        /**
         * Sanitize all fields. The fields can be `null` meaning they
         * were not defined in the input array. If the value exists
         * then it will be sanitized.
         *
         * If there is no sanitizer defined, the value will be left intact.
         */
        /** @var SanitizerEnumInterface $field */
        foreach ($fields as $field) {
            $value = $input[$field->name] ?? $field->default();

            if (null !== $value) {
                $sanitizer = $field->sanitizer();
                if (true !== empty($sanitizer)) {
                    $value = $this->filter->sanitize($value, $sanitizer);
                }
            }

            $sanitized[$field->name] = $value;
        }

        /**
         * Return sanitized array
         */
        /** @var TInputSanitize $sanitized */
        return $sanitized;
    }
}
