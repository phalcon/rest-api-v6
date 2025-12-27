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

namespace Phalcon\Api\Domain\Infrastructure\Env\Adapters;

use Phalcon\Api\Domain\Infrastructure\Env\EnvManagerTypes;

/**
 * @phpstan-import-type TDotEnvOptions from EnvManagerTypes
 * @phpstan-import-type TSettings from EnvManagerTypes
 */
interface AdapterInterface
{
    /**
     * @param TDotEnvOptions $options
     *
     * @return TSettings
     */
    public function load(array $options): array;
}
