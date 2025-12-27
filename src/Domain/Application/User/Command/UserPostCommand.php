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

namespace Phalcon\Api\Domain\Application\User\Command;

use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;

final readonly class UserPostCommand implements CommandInterface
{
    /**
     * @param int|null    $id
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
        public ?int $id,
        public int $status,
        public ?string $email,
        public ?string $password,
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
}
