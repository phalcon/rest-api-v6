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
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\SanitizerInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\ValueObjectInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;

use function get_object_vars;

/**
 * Base factory for value objects such as input DTOs.
 *
 * Concrete DTOs must implement protected static function
 *
 * `fromArray(array $sanitized): static`
 *
 * and keep themselves immutable/read\-only.
 *
 * @phpstan-import-type TInputSanitize from InputTypes
 * @phpstan-import-type TAuthInput from InputTypes
 * @phpstan-import-type TCompany from CompanyTypes
 * @phpstan-import-type TUser from UserTypes
 * @phpstan-type TInputs TAuthInput|TCompany|TUser
 */
abstract class AbstractInputValueObject implements ValueObjectInterface
{
    /**
     * Factory that accepts a SanitizerInterface and returns the concrete DTO.
     *
     * @param SanitizerInterface $sanitizer
     * @param TInputSanitize     $input
     *
     * @return static
     */
    public static function new(SanitizerInterface $sanitizer, array $input): static
    {
        $sanitized = $sanitizer->sanitize($input);

        return static::fromArray($sanitized);
    }

    /**
     * @return TInputs
     */
    public function toArray(): array
    {
        /** @var TInputs $vars */
        $vars = get_object_vars($this);

        return $vars;
    }

    /**
     * Build the concrete DTO from a sanitized array.
     *
     * @param TInputSanitize $sanitized
     *
     * @return static
     */
    abstract protected static function fromArray(array $sanitized): static;
}
