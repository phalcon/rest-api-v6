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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Validation;

use Phalcon\Api\Domain\ADR\InputTypes;

/**
 * @phpstan-import-type TValidatorErrors from InputTypes
 * @phpstan-type TResultMeta array<string, mixed>
 */
final class Result
{
    /**
     * @param TValidatorErrors $errors
     * @param TResultMeta      $meta
     */
    public function __construct(
        private readonly array $errors = [],
        private array $meta = [],
    ) {
    }

    /**
     * Create a failure result.
     *
     * @param TValidatorErrors $errors
     *
     * @return self
     */
    public static function error(array $errors): self
    {
        return new self($errors);
    }

    /**
     * @return TValidatorErrors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string     $key
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    public function getMeta(string $key, mixed $defaultValue = null): mixed
    {
        return $this->meta[$key] ?? $defaultValue;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->errors === [];
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function setMeta(string $key, mixed $value): void
    {
        $this->meta[$key] = $value;
    }

    /**
     * @return self
     */
    public static function success(): self
    {
        return new self([]);
    }
}
