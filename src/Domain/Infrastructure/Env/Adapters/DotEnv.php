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

use Dotenv\Dotenv as ParentDotEnv;
use Exception;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManagerTypes;
use Phalcon\Api\Domain\Infrastructure\Exceptions\InvalidConfigurationArgumentException;

/**
 * @phpstan-import-type TDotEnvOptions from EnvManagerTypes
 * @phpstan-import-type TSettings from EnvManagerTypes
 */
class DotEnv implements AdapterInterface
{
    /**
     * @param TDotEnvOptions $options
     *
     * @return TSettings
     * @throws Exception
     */
    public function load(array $options): array
    {
        /** @var string|null $filePath */
        $filePath = $options['filePath'] ?? null;
        if (true === empty($filePath) || true !== file_exists($filePath)) {
            throw InvalidConfigurationArgumentException::new(
                'The .env file does not exist at the specified path: '
                . (string)$filePath
            );
        }

        $dotenv = ParentDotEnv::createMutable($filePath);
        $dotenv->load();

        /** @var TSettings $env */
        $env = $_ENV;

        return $env;
    }
}
