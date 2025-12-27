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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\AbstractInputValueObject;

/**
 * Value object for input
 *
 * @phpstan-import-type TUserInput from InputTypes
 */
final class UserInput extends AbstractInputValueObject
{
    /**
     * @param int         $id
     * @param int         $status
     * @param string|null $email
     * @param string|null $password
     * @param string|null $namePrefix
     * @param string|null $nameFirst
     * @param string|null $nameMiddle
     * @param string|null $nameLast
     * @param string|null $nameSuffix
     * @param string|null $issuer
     * @param string|null $tokenPassword
     * @param string|null $tokenId
     * @param string|null $preferences
     * @param string|null $createdDate
     * @param int|null    $createdUserId
     * @param string|null $updatedDate
     * @param int|null    $updatedUserId
     */
    public function __construct(
        public readonly int $id,
        public readonly int $status,
        public readonly ?string $email,
        public readonly ?string $password,
        public readonly ?string $namePrefix,
        public readonly ?string $nameFirst,
        public readonly ?string $nameMiddle,
        public readonly ?string $nameLast,
        public readonly ?string $nameSuffix,
        public readonly ?string $issuer,
        public readonly ?string $tokenPassword,
        public readonly ?string $tokenId,
        public readonly ?string $preferences,
        public readonly ?string $createdDate,
        public readonly ?int $createdUserId,
        public readonly ?string $updatedDate,
        public readonly ?int $updatedUserId,
    ) {
    }

    /**
     * Build the concrete DTO from a sanitized array.
     *
     * @param TUserInput $sanitized
     *
     * @return static
     */
    protected static function fromArray(array $sanitized): static
    {
        $id     = isset($sanitized['id']) ? (int)$sanitized['id'] : 0;
        $status = isset($sanitized['status']) ? (int)$sanitized['status'] : 0;

        $createdUserId = isset($sanitized['createdUserId']) ? (int)$sanitized['createdUserId'] : 0;
        $updatedUserId = isset($sanitized['updatedUserId']) ? (int)$sanitized['updatedUserId'] : 0;

        return new self(
            $id,
            $status,
            isset($sanitized['email']) ? (string)$sanitized['email'] : null,
            isset($sanitized['password']) ? (string)$sanitized['password'] : null,
            isset($sanitized['namePrefix']) ? (string)$sanitized['namePrefix'] : null,
            isset($sanitized['nameFirst']) ? (string)$sanitized['nameFirst'] : null,
            isset($sanitized['nameMiddle']) ? (string)$sanitized['nameMiddle'] : null,
            isset($sanitized['nameLast']) ? (string)$sanitized['nameLast'] : null,
            isset($sanitized['nameSuffix']) ? (string)$sanitized['nameSuffix'] : null,
            isset($sanitized['issuer']) ? (string)$sanitized['issuer'] : null,
            isset($sanitized['tokenPassword']) ? (string)$sanitized['tokenPassword'] : null,
            isset($sanitized['tokenId']) ? (string)$sanitized['tokenId'] : null,
            isset($sanitized['preferences']) ? (string)$sanitized['preferences'] : null,
            isset($sanitized['createdDate']) ? (string)$sanitized['createdDate'] : null,
            $createdUserId,
            isset($sanitized['updatedDate']) ? (string)$sanitized['updatedDate'] : null,
            $updatedUserId
        );
    }
}
