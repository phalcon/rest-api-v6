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

namespace Phalcon\Api\Domain\Application\Auth\Handler;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\Infrastructure\CommandBus\HandlerInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Transformer\AuthTransformer;
use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\ValidatorInterface;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;

/**
 * @phpstan-import-type TAuthLogoutInput from InputTypes
 */
abstract class AbstractAuthLogoutRefreshHandler implements HandlerInterface
{
    /**
     * @param TokenManagerInterface $tokenManager
     * @param AuthTransformer       $transformer
     * @param ValidatorInterface    $validator
     */
    public function __construct(
        protected readonly TokenManagerInterface $tokenManager,
        protected readonly AuthTransformer $transformer,
        protected readonly ValidatorInterface $validator,
    ) {
    }
}
