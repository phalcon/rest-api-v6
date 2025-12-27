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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Company\DTO;

use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\Company;
use Phalcon\Api\Tests\AbstractUnitTestCase;

use function rand;

final class CompanyTest extends AbstractUnitTestCase
{
    public function testObject(): void
    {
        $companyData           = $this->getNewCompanyData();
        $companyData['com_id'] = rand(1, 100);

        $company = new Company(
            $companyData['com_id'],
            $companyData['com_name'],
            $companyData['com_phone'],
            $companyData['com_email'],
            $companyData['com_website'],
            $companyData['com_address_line_1'],
            $companyData['com_address_line_2'],
            $companyData['com_city'],
            $companyData['com_state_province'],
            $companyData['com_zip_code'],
            $companyData['com_country'],
            $companyData['com_created_date'],
            $companyData['com_created_usr_id'],
            $companyData['com_updated_date'],
            $companyData['com_updated_usr_id'],
        );


        $expected = [
            'id'            => $companyData['com_id'],
            'name'          => $companyData['com_name'],
            'phone'         => $companyData['com_phone'],
            'email'         => $companyData['com_email'],
            'website'       => $companyData['com_website'],
            'addressLine1'  => $companyData['com_address_line_1'],
            'addressLine2'  => $companyData['com_address_line_2'],
            'city'          => $companyData['com_city'],
            'stateProvince' => $companyData['com_state_province'],
            'zipCode'       => $companyData['com_zip_code'],
            'country'       => $companyData['com_country'],
            'createdDate'   => $companyData['com_created_date'],
            'createdUserId' => $companyData['com_created_usr_id'],
            'updatedDate'   => $companyData['com_updated_date'],
            'updatedUserId' => $companyData['com_updated_usr_id'],
        ];
        $actual   = $company->toArray();
        $this->assertSame($expected, $actual);
    }
}
