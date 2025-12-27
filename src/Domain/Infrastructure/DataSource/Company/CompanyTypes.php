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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Company;

/**
 * @phpstan-type TCompanyDbRecord array{
 *     com_id: int,
 *     com_name: string,
 *     com_phone: string,
 *     com_email: string,
 *     com_website: string,
 *     com_address_line_1: string,
 *     com_address_line_2: string,
 *     com_city: string,
 *     com_state_province: string,
 *     com_zip_code: string,
 *     com_country: string,
 *     com_created_date: string,
 *     com_created_usr_id: int,
 *     com_updated_date: string,
 *     com_updated_usr_id: int
 * }
 *
 * @phpstan-type TCompanyDomainToDbRecord array{
 *     com_id: int,
 *     com_name: ?string,
 *     com_phone: ?string,
 *     com_email: ?string,
 *     com_website: ?string,
 *     com_address_line_1: ?string,
 *     com_address_line_2: ?string,
 *     com_city: ?string,
 *     com_state_province: ?string,
 *     com_zip_code: ?string,
 *     com_country: ?string,
 *     com_created_date: ?string,
 *     com_created_usr_id: ?int,
 *     com_updated_date: ?string,
 *     com_updated_usr_id: ?int
 * }
 *
 * @phpstan-type TCompanyDbRecordOptional array{
 *     com_id?: int,
 *     com_name?: ?string,
 *     com_phone?: ?string,
 *     com_email?: ?string,
 *     com_website?: ?string,
 *     com_address_line_1?: ?string,
 *     com_address_line_2?: ?string,
 *     com_city?: ?string,
 *     com_state_province?: ?string,
 *     com_zip_code?: ?string,
 *     com_country?: ?string,
 *     com_created_date?: ?string,
 *     com_created_usr_id?: ?int,
 *     com_updated_date?: ?string,
 *     com_updated_usr_id?: ?int
 * }
 *
 * @phpstan-type TCompany array{
 *     id: int,
 *     name: ?string,
 *     phone: ?string,
 *     email: ?string,
 *     website: ?string,
 *     addressLine1: ?string,
 *     addressLine2: ?string,
 *     city: ?string,
 *     stateProvince: ?string,
 *     zipCode: ?string,
 *     country: ?string,
 *     createdDate: ?string,
 *     createdUserId: ?int,
 *     updatedDate: ?string,
 *     updatedUserId: ?int
 * }
 *
 * @phpstan-type TCompanyRecord array{}|TCompanyDbRecord
 * @phpstan-type TCompanyRecords array{}|TCompanyDbRecord[]
 *
 * @phpstan-type TCriteria array<string, bool|int|string|null>
 */
final class CompanyTypes
{
}
