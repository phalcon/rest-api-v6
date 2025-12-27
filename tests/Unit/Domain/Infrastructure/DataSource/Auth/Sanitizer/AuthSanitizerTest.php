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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Auth\Sanitizer;

use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Sanitizer\AuthSanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Filter;

final class AuthSanitizerTest extends AbstractUnitTestCase
{
    public function testEmpty(): void
    {
        /** @var Filter $filter */
        $filter    = $this->container->getShared(Container::FILTER);
        $sanitizer = new AuthSanitizer($filter);

        $expected = [
            'email'    => null,
            'password' => null,
            'token'    => null,
        ];
        $actual   = $sanitizer->sanitize([]);
        $this->assertSame($expected, $actual);
    }

    public function testObject(): void
    {
        /** @var Filter $filter */
        $filter    = $this->container->getShared(Container::FILTER);
        $sanitizer = new AuthSanitizer($filter);

        $userData = [
            'email'    => 'John.Doe (newsletter) +spam@example.COM',
            'password' => 'some <value>',
            'token'    => 'some <value>',
        ];

        $expected = [
            'email'    => 'John.Doenewsletter+spam@example.COM',
            'password' => 'some <value>',
            'token'    => 'some <value>',
        ];
        $actual   = $sanitizer->sanitize($userData);
        $this->assertSame($expected, $actual);
    }
}
