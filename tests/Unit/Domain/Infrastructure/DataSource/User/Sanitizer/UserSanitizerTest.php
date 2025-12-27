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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\User\Sanitizer;

use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Sanitizer\UserSanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Filter;

final class UserSanitizerTest extends AbstractUnitTestCase
{
    public function testEmpty(): void
    {
        /** @var Filter $filter */
        $filter    = $this->container->getShared(Container::FILTER);
        $sanitizer = new UserSanitizer($filter);

        $expected = [
            'id'            => 0,
            'status'        => 0,
            'email'         => null,
            'password'      => null,
            'namePrefix'    => null,
            'nameFirst'     => null,
            'nameLast'      => null,
            'nameMiddle'    => null,
            'nameSuffix'    => null,
            'issuer'        => null,
            'tokenPassword' => null,
            'tokenId'       => null,
            'preferences'   => null,
            'createdDate'   => null,
            'createdUserId' => 0,
            'updatedDate'   => null,
            'updatedUserId' => 0,
        ];
        $actual   = $sanitizer->sanitize([]);
        $this->assertSame($expected, $actual);
    }

    public function testObject(): void
    {
        /** @var Filter $filter */
        $filter    = $this->container->getShared(Container::FILTER);
        $sanitizer = new UserSanitizer($filter);

        $userData = [
            'id'            => '123',
            'status'        => '4',
            'email'         => 'John.Doe (newsletter) +spam@example.COM',
            'password'      => 'some <value>',
            'namePrefix'    => 'some <value>',
            'nameFirst'     => 'some <value>',
            'nameLast'      => 'some <value>',
            'nameMiddle'    => 'some <value>',
            'nameSuffix'    => 'some <value>',
            'issuer'        => 'some <value>',
            'tokenPassword' => 'some <value>',
            'tokenId'       => 'some <value>',
            'preferences'   => 'some <value>',
            'createdDate'   => 'some <value>',
            'createdUserId' => '123',
            'updatedDate'   => 'some <value>',
            'updatedUserId' => '123',
        ];

        $expected = [
            'id'            => 123,
            'status'        => 4,
            'email'         => 'John.Doenewsletter+spam@example.COM',
            'password'      => 'some <value>',
            'namePrefix'    => 'some ',
            'nameFirst'     => 'some ',
            'nameLast'      => 'some ',
            'nameMiddle'    => 'some ',
            'nameSuffix'    => 'some ',
            'issuer'        => 'some <value>',
            'tokenPassword' => 'some <value>',
            'tokenId'       => 'some <value>',
            'preferences'   => 'some ',
            'createdDate'   => 'some ',
            'createdUserId' => 123,
            'updatedDate'   => 'some ',
            'updatedUserId' => 123,
        ];
        $actual   = $sanitizer->sanitize($userData);
        $this->assertSame($expected, $actual);
    }
}
