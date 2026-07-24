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

use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Responder\ResponderTypes;
use Phalcon\Domain\Payload\Payload as PhalconPayload;
use Phalcon\Domain\Payload\Status;

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

        /**
         * The parent exposes setters rather than a constructor, and carries
         * the result under "output".
         */
        $this->setStatus($status);
        $this->setOutput($result);
    }

    /**
     * @param TData $data
     *
     * @return self
     */
    public static function created(array $data): self
    {
        return new self(Status::CREATED, $data);
    }

    /**
     * @param TData $data
     *
     * @return self
     */
    public static function deleted(array $data): self
    {
        return new self(Status::DELETED, $data);
    }

    /**
     * @param TErrors $errors
     *
     * @return self
     */
    public static function error(array $errors): self
    {
        return new self(status: Status::ERROR, errors: $errors);
    }

    /**
     * @param TErrors $errors
     *
     * @return self
     */
    public static function invalid(array $errors): self
    {
        return new self(status: Status::NOT_VALID, errors: $errors);
    }

    /**
     * @return self
     */
    public static function notFound(): self
    {
        return new self(
            status: Status::NOT_FOUND,
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
        return new self(Status::SUCCESS, $data);
    }

    /**
     * @param TErrors $errors
     *
     * @return self
     */
    public static function unauthorized(array $errors): self
    {
        return new self(
            status: Status::NOT_AUTHORIZED,
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
            Status::UPDATED,
            [
                'data' => $data,
            ]
        );
    }

    /**
     * The result assembled by the constructor. The parent stores it as its
     * "output" and types the accessor as mixed; this narrows it back to an
     * array for the responder.
     *
     * @return array<array-key, mixed>
     */
    public function getResult(): array
    {
        /** @var array<array-key, mixed> $output */
        $output = $this->getOutput();

        return $output;
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
