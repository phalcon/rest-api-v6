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

use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Sanitizer\CompanySanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Filter;
use Phalcon\Filter\FilterInterface;

final class CompanySanitizerTest extends AbstractUnitTestCase
{
    public function testEmpty(): void
    {
        /** @var Filter $filter */
        $filter    = $this->container->get(FilterInterface::class);
        $sanitizer = new CompanySanitizer($filter);

        $expected = [
            'addressLine1'  => null,
            'addressLine2'  => null,
            'city'          => null,
            'country'       => null,
            'createdDate'   => null,
            'createdUserId' => 0,
            'email'         => null,
            'id'            => 0,
            'name'          => null,
            'page'          => 1,
            'perPage'       => 10,
            'phone'         => null,
            'stateProvince' => null,
            'updatedDate'   => null,
            'updatedUserId' => 0,
            'website'       => null,
            'zipCode'       => null,
        ];
        $actual   = $sanitizer->sanitize([]);
        $this->assertSame($expected, $actual);
    }

    public function testObject(): void
    {
        /** @var Filter $filter */
        $filter    = $this->container->get(FilterInterface::class);
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
            'addressLine1'  => 'some ',
            'addressLine2'  => 'some ',
            'city'          => 'some ',
            'country'       => 'some ',
            'createdDate'   => 'some ',
            'createdUserId' => 123,
            'email'         => 'John.Doenewsletter+spam@example.COM',
            'id'            => 123,
            'name'          => 'some ',
            'page'          => 1,
            'perPage'       => 10,
            'phone'         => 'some ',
            'stateProvince' => 'some ',
            'updatedDate'   => null,
            'updatedUserId' => 123,
            'website'       => 'some ',
            'zipCode'       => 'some ',
        ];
        $actual   = $sanitizer->sanitize($companyData);
        $this->assertSame($expected, $actual);
    }
}
