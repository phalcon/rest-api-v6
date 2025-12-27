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

use Phalcon\Api\Domain\Infrastructure\Env\Adapters\AdapterInterface;
use Phalcon\Api\Domain\Infrastructure\Env\Adapters\DotEnv;
use Phalcon\Api\Domain\Infrastructure\Exceptions\InvalidConfigurationArgumentException;

class EnvFactory
{
    /**
     * @var array<string, AdapterInterface>
     */
    protected array $instances = [];

    public function newInstance(string $name, mixed ...$parameters): AdapterInterface
    {
        $adapters = $this->getAdapters();
        if (true !== isset($this->instances[$name])) {
            if (true !== isset($adapters[$name])) {
                throw InvalidConfigurationArgumentException::new(
                    'Service ' . $name . ' is not registered'
                );
            }

            $definition = $adapters[$name];
            /** @var AdapterInterface $instance */
            $instance               = new $definition(...$parameters);
            $this->instances[$name] = $instance;
        }

        return $this->instances[$name];
    }

    /**
     * @return array<string, class-string>
     */
    protected function getAdapters(): array
    {
        return [
            'dotenv' => DotEnv::class,
        ];
    }
}
