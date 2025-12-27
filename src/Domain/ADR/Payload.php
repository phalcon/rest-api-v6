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

namespace Phalcon\Api\Domain\ADR;

use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Responder\ResponderTypes;
use Phalcon\Domain\Payload as PhalconPayload;

use function array_key_exists;

/**
 * @phpstan-import-type TData from ResponderTypes
 * @phpstan-import-type TErrors from ResponderTypes
 * @phpstan-import-type TResponsePayload from ResponderTypes
 * @phpstan-type TPayloadDataInput TData|TResponsePayload
 * @phpstan-type TPayloadErrorInput TErrors|TResponsePayload
 */
final class Payload extends PhalconPayload
{
    /**
     * @param string            $status
     * @param TPayloadDataInput $data
     * @param TPayloadDataInput $errors
     */
    private function __construct(
        string $status,
        array $data = [],
        array $errors = []
    ) {
        $result = [];
        $result = $this->mergePart($result, $data, 'data');
        $result = $this->mergePart($result, $errors, 'errors');

        parent::__construct($status, $result);
    }

    /**
     * @param TData $data
     *
     * @return self
     */
    public static function created(array $data): self
    {
        return new self(DomainStatus::CREATED, $data);
    }

    /**
     * @param TData $data
     *
     * @return self
     */
    public static function deleted(array $data): self
    {
        return new self(DomainStatus::DELETED, $data);
    }

    /**
     * @param TErrors $errors
     *
     * @return self
     */
    public static function error(array $errors): self
    {
        return new self(status: DomainStatus::ERROR, errors: $errors);
    }

    /**
     * @param TErrors $errors
     *
     * @return self
     */
    public static function invalid(array $errors): self
    {
        return new self(status: DomainStatus::INVALID, errors: $errors);
    }

    /**
     * @return self
     */
    public static function notFound(): self
    {
        return new self(
            status: DomainStatus::NOT_FOUND,
            errors: [
                'code'    => HttpCodesEnum::NotFound->value,
                'message' => HttpCodesEnum::NotFound->text(),
                'data'    => [],
                'errors'  => [['Record(s) not found']],
            ]
        );
    }

    /**
     * @param TData $data
     *
     * @return self
     */
    public static function success(array $data): self
    {
        return new self(DomainStatus::SUCCESS, $data);
    }

    /**
     * @param TErrors $errors
     *
     * @return self
     */
    public static function unauthorized(array $errors): self
    {
        return new self(
            status: DomainStatus::UNAUTHORIZED,
            errors: [
                'code'    => HttpCodesEnum::Unauthorized->value,
                'message' => HttpCodesEnum::Unauthorized->text(),
                'data'    => [],
                'errors'  => $errors,
            ]
        );
    }

    /**
     * @param TData $data
     *
     * @return self
     */
    public static function updated(array $data): self
    {
        return new self(
            DomainStatus::UPDATED,
            [
                'data' => $data,
            ]
        );
    }

    /**
     * Merge a part into the result. If the part already contains the
     * $key, assume it's a preformatted payload and return it. Otherwise
     * attach the part under the $key.
     *
     * @param TPayloadDataInput $existing
     * @param TPayloadDataInput $element
     * @param string            $key
     *
     * @return TPayloadDataInput
     */
    private function mergePart(
        array $existing,
        array $element,
        string $key
    ): array {
        if (empty($element)) {
            return $existing;
        }

        if (array_key_exists($key, $element)) {
            return $element;
        }

        /**
         * preserve any existing keys in $existing and add the new named key
         */
        $existing[$key] = $element;

        return $existing;
    }
}
