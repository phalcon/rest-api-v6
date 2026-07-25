<?php

declare(strict_types=1);

use Phalcon\Api\Front\ApiFront;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * run() returns a status code for a CLI caller. Under FPM nothing reads it,
 * and exiting here would kill the dispatch tests that require this file in
 * a child process to exercise the stack end to end.
 */
(new ApiFront(dirname(__DIR__)))->run();
