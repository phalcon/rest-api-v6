<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Phalcon\Talon\Settings;
use Phalcon\Talon\Talon;

$root = dirname(__DIR__);

require_once $root . '/vendor/autoload.php';

/**
 * The application writes logs while it is exercised in-process; make sure the
 * target directories exist before any test runs.
 */
@mkdir($root . '/storage/logs', 0775, true);
@mkdir($root . '/tests/_output', 0775, true);

/**
 * Load the test environment into $_ENV. Real OS/CI variables already present win
 * (createImmutable never overwrites them), so the same suite runs in docker
 * (service-name DB host) and native CI (127.0.0.1) unchanged.
 */
Dotenv::createImmutable(__DIR__, '.env.test')->safeLoad();

Talon::boot(Settings::fromEnv());
