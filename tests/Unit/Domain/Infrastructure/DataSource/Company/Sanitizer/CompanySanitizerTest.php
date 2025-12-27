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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Company\Sanitizer;

use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Sanitizer\CompanySanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Filter;

final class CompanySanitizerTest extends AbstractUnitTestCase
{
    public function testEmpty(): void
    {
        /** @var Filter $filter */
        $filter    = $this->container->getShared(Container::FILTER);
        $sanitizer = new CompanySanitizer($filter);

        $expected = [
            'id'            => 0,
            'name'          => null,
            'phone'         => null,
            'email'         => null,
            'website'       => null,
            'addressLine1'  => null,
            'addressLine2'  => null,
            'city'          => null,
            'stateProvince' => null,
            'zipCode'       => null,
            'country'       => null,
            'createdDate'   => null,
            'createdUserId' => 0,
            'updatedDate'   => null,
            'updatedUserId' => 0,
            'page'          => 1,
            'perPage'       => 10,
        ];
        $actual   = $sanitizer->sanitize([]);
        $this->assertSame($expected, $actual);
    }

    public function testObject(): void
    {
        /** @var Filter $filter */
        $filter    = $this->container->getShared(Container::FILTER);
        $sanitizer = new CompanySanitizer($filter);

        $companyData = [
            'id'            => '123',
            'name'          => 'some <value>',
            'phone'         => 'some <value>',
            'email'         => 'John.Doe (newsletter) +spam@example.COM',
            'website'       => 'some <value>',
            'addressLine1'  => 'some <value>',
            'addressLine2'  => 'some <value>',
            'city'          => 'some <value>',
            'stateProvince' => 'some <value>',
            'zipCode'       => 'some <value>',
            'country'       => 'some <value>',
            'createdDate'   => 'some <value>',
            'createdUserId' => '123',
            'updatedDate'   => null,
            'updatedUserId' => '123',
            'page'          => '1',
            'perPage'       => '10',
        ];

        $expected = [
            'id'            => 123,
            'name'          => 'some ',
            'phone'         => 'some ',
            'email'         => 'John.Doenewsletter+spam@example.COM',
            'website'       => 'some ',
            'addressLine1'  => 'some ',
            'addressLine2'  => 'some ',
            'city'          => 'some ',
            'stateProvince' => 'some ',
            'zipCode'       => 'some ',
            'country'       => 'some ',
            'createdDate'   => 'some ',
            'createdUserId' => 123,
            'updatedDate'   => null,
            'updatedUserId' => 123,
            'page'          => 1,
            'perPage'       => 10,
        ];
        $actual   = $sanitizer->sanitize($companyData);
        $this->assertSame($expected, $actual);
    }
}
