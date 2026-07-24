<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$root = dirname(__DIR__);

require_once $root . '/vendor/autoload.php';

// Real OS/CI environment variables win over the .env file
$envs = array_merge(getenv(), $_ENV);

Dotenv::createImmutable($root)->load();

$_ENV = array_merge($envs, $_ENV);

return [
    'paths'         => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds'      => __DIR__ . '/db/seeds',
    ],
    'environments'  => [
        'default_migration_table' => 'ut_migrations',
        'default_environment'     => 'development',
        'development'             => [
            'adapter' => $_ENV['DB_ADAPTER'] ?? 'mysql',
            'host'    => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'name'    => $_ENV['DB_NAME'] ?? 'phalcon',
            'user'    => $_ENV['DB_USER'] ?? 'root',
            'pass'    => $_ENV['DB_PASS'] ?? 'secret',
            'port'    => $_ENV['DB_PORT'] ?? 3306,
            'charset' => $_ENV['DB_CHARSET'] ?? 'utf8',
        ],
    ],
    'version_order' => 'creation',
];
