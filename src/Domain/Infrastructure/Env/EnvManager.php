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

namespace Phalcon\Api\Domain\Infrastructure\Env;

use Phalcon\Api\Domain\Infrastructure\Constants\Dates;
use Phalcon\Support\Collection;

use function array_merge;
use function getenv;

/**
 * @phpstan-import-type TSettings from EnvManagerTypes
 */
class EnvManager extends Collection
{
    public function __construct()
    {
        parent::__construct();

        $this->load();
    }

    /**
     * @return string
     */
    public function appEnv(): string
    {
        /** @var string $appEnv */
        $appEnv = $this->get('APP_ENV', 'development');

        return (string)$appEnv;
    }

    /**
     * @return int
     */
    public function appLogLevel(): int
    {
        /** @var int $logLevel */
        $logLevel = $this->get('APP_LOG_LEVEL', 1, 'int');

        return $logLevel;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function appPath(string $path = ''): string
    {
        return dirname(__DIR__, 4)
            . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * @return string
     */
    public function appTimezone(): string
    {
        /** @var string $timezone */
        $timezone = $this->get('APP_TIMEZONE', Dates::DATE_TIME_ZONE);

        return (string)$timezone;
    }

    /**
     * @return void
     */
    public function load(): void
    {
        $envFactory = new EnvFactory();
        $options    = $this->getOptions();
        $adapter    = $options['adapter'];

        $envs = array_merge(getenv(), $_ENV);
        /** @var TSettings $options */
        $options = $envFactory->newInstance($adapter)->load($options);
        /** @var TSettings $envs */
        $envs = array_merge($envs, $options);

        $settings = array_map(
            function ($value) {
                return match ($value) {
                    'true'  => true,
                    'false' => false,
                    default => $value,
                };
            },
            $envs
        );

        $this->clear();
        $this->init($settings);
    }

    /**
     * @return array<string, string>
     */
    private function getOptions(): array
    {
        $envs = array_merge(getenv(), $_ENV);
        /** @var string $adapter */
        $adapter = $envs['APP_ENV_ADAPTER'] ?? 'dotenv';
        /** @var string $filePath */
        $filePath = $envs['APP_ENV_FILE_PATH'] ?? $this->appPath();

        return [
            'adapter'  => $adapter,
            'filePath' => $filePath,
        ];
    }
}
