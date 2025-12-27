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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Enums\Http;

use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class HttpCodesEnumTest extends AbstractUnitTestCase
{
    /**
     * @return array[]
     */
    public static function getExamples(): array
    {
        return [
            [
                HttpCodesEnum::OK,
                200,
                'OK',
            ],
            [
                HttpCodesEnum::BadRequest,
                400,
                'Bad Request',
            ],
            [
                HttpCodesEnum::NotFound,
                404,
                'Not Found',
            ],
            [
                HttpCodesEnum::Unauthorized,
                401,
                'Unauthorized',
            ],
            [
                HttpCodesEnum::AppMalformedPayload,
                3400,
                'Malformed payload',
            ],
            [
                HttpCodesEnum::AppRecordsNotFound,
                3401,
                'Record(s) not found',
            ],
            [
                HttpCodesEnum::AppResourceNotFound,
                3402,
                'Resource not found',
            ],
            [
                HttpCodesEnum::AppUnauthorized,
                3403,
                'Unauthorized',
            ],
            [
                HttpCodesEnum::AppCannotConnectToDatabase,
                3404,
                'Failure connecting to the database',
            ],
            [
                HttpCodesEnum::AppCannotCreateDatabaseRecord,
                3405,
                'Cannot create database record: ',
            ],
            [
                HttpCodesEnum::AppCannotDeleteDatabaseRecord,
                3406,
                'Cannot delete database record: ',
            ],
            [
                HttpCodesEnum::AppCannotUpdateDatabaseRecord,
                3407,
                'Cannot update database record: ',
            ],
            [
                HttpCodesEnum::AppIncorrectCredentials,
                3408,
                'Incorrect credentials',
            ],
            [
                HttpCodesEnum::AppInvalidArguments,
                3409,
                'Invalid arguments provided',
            ],
            [
                HttpCodesEnum::AppTokenInvalidAudience,
                3410,
                'Invalid Token [Audience]',
            ],
            [
                HttpCodesEnum::AppTokenInvalidCannotDecodeContent,
                3411,
                'Invalid Token [Cannot decode content]',
            ],
            [
                HttpCodesEnum::AppTokenInvalidIdentifier,
                3412,
                'Invalid Token [Identifier]',
            ],
            [
                HttpCodesEnum::AppTokenInvalidIssuer,
                3413,
                'Invalid Token [Issuer]',
            ],
            [
                HttpCodesEnum::AppTokenInvalidStructure,
                3414,
                'Invalid Token [Token structure]',
            ],
            [
                HttpCodesEnum::AppTokenInvalidTenant,
                3415,
                'Invalid Token [Tenant]',
            ],
            [
                HttpCodesEnum::AppTokenInvalidUser,
                3416,
                'Invalid Token [User]',
            ],
            [
                HttpCodesEnum::AppTokenNotPresent,
                3417,
                'Invalid Token [Token not present]',
            ],
            [
                HttpCodesEnum::AppTokenNotValid,
                3418,
                'Invalid Token [verification - Token not valid]',
            ],
            [
                HttpCodesEnum::AppTokenSignatureNotValid,
                3419,
                'Invalid Token [verification - Signature not valid]',
            ],
            [
                HttpCodesEnum::AppTokenUnsupportedHeaderFound,
                3420,
                'Invalid Token [Unsupported header]',
            ],
            [
                HttpCodesEnum::AppValidationFailed,
                3421,
                'Validation error [%s]',
            ],
        ];
    }

    public function testCheckCount(): void
    {
        $expected = 26;
        $actual   = HttpCodesEnum::cases();
        $this->assertCount($expected, $actual);
    }

    #[DataProvider('getExamples')]
    public function testCheckItems(
        HttpCodesEnum $element,
        int $value,
        string $text
    ): void {
        $expected = $value;
        $actual   = $element->value;
        $this->assertSame($expected, $actual);

        $expected = $text;
        $actual   = $element->text();
        $this->assertSame($expected, $actual);

        $expected = [$value => $text];
        $actual   = $element->error();
        $this->assertSame($expected, $actual);
    }
}
