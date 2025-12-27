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

use Phalcon\Api\Domain\Infrastructure\DataSource\AbstractValueObject;

use function trim;

final class User extends AbstractValueObject
{
    /**
     * @param int         $id
     * @param int         $status
     * @param string      $email
     * @param string      $password
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
        public int $id,
        public int $status,
        public string $email,
        public string $password,
        public ?string $namePrefix,
        public ?string $nameFirst,
        public ?string $nameMiddle,
        public ?string $nameLast,
        public ?string $nameSuffix,
        public ?string $issuer,
        public ?string $tokenPassword,
        public ?string $tokenId,
        public ?string $preferences,
        public ?string $createdDate,
        public ?int $createdUserId,
        public ?string $updatedDate,
        public ?int $updatedUserId,
    ) {
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return trim(
            ($this->nameLast ?? '') . ', ' . ($this->nameFirst ?? '') . ' ' . ($this->nameMiddle ?? '')
        );
    }
}
