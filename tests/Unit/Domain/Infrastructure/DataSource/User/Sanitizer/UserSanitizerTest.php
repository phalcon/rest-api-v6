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

use Phalcon\Api\Domain\Infrastructure\DataSource\User\Sanitizer\UserSanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Filter;
use Phalcon\Filter\FilterInterface;

final class UserSanitizerTest extends AbstractUnitTestCase
{
    public function testEmpty(): void
    {
        /** @var Filter $filter */
        $filter    = $this->container->get(FilterInterface::class);
        $sanitizer = new UserSanitizer($filter);

        $expected = [
            'createdDate'   => null,
            'createdUserId' => 0,
            'email'         => null,
            'id'            => 0,
            'issuer'        => null,
            'nameFirst'     => null,
            'nameLast'      => null,
            'nameMiddle'    => null,
            'namePrefix'    => null,
            'nameSuffix'    => null,
            'password'      => null,
            'preferences'   => null,
            'status'        => 0,
            'tokenId'       => null,
            'tokenPassword' => null,
            'updatedDate'   => null,
            'updatedUserId' => 0,
        ];
        $actual   = $sanitizer->sanitize([]);
        $this->assertSame($expected, $actual);
    }

    public function testObject(): void
    {
        /** @var Filter $filter */
        $filter    = $this->container->get(FilterInterface::class);
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
            'createdDate'   => 'some ',
            'createdUserId' => 123,
            'email'         => 'John.Doenewsletter+spam@example.COM',
            'id'            => 123,
            'issuer'        => 'some <value>',
            'nameFirst'     => 'some ',
            'nameLast'      => 'some ',
            'nameMiddle'    => 'some ',
            'namePrefix'    => 'some ',
            'nameSuffix'    => 'some ',
            'password'      => 'some <value>',
            'preferences'   => 'some ',
            'status'        => 4,
            'tokenId'       => 'some <value>',
            'tokenPassword' => 'some <value>',
            'updatedDate'   => 'some ',
            'updatedUserId' => 123,
        ];
        $actual   = $sanitizer->sanitize($userData);
        $this->assertSame($expected, $actual);
    }
}
