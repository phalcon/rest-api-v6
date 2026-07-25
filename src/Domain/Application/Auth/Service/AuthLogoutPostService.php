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

namespace Phalcon\Api\Domain\Application\Auth\Service;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Contracts\ADR\Payload\Payload as PayloadContract;

/**
 * @phpstan-import-type TAuthLogoutInput from InputTypes
 */
final class AuthLogoutPostService extends AbstractAuthService
{
    /**
     * @param TAuthLogoutInput $input
     *
     * @return PayloadContract
     */
    public function __invoke(array $input): PayloadContract
    {
        return $this->facade->logout($input);
    }
}
