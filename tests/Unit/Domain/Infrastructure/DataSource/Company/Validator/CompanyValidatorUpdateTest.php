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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Company\Validator;

use Faker\Factory as FakerFactory;
use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactory;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Validator\CompanyValidatorUpdate;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class CompanyValidatorUpdateTest extends AbstractUnitTestCase
{
    public function testError(): void
    {
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);
        /** @var CompanyValidatorUpdate $validator */
        $validator = $this->container->get(CompanyValidatorUpdate::class);

        $input        = [];
        $companyInput = $factory->update($input);

        $result = $validator->validate($companyInput);
        $actual = $result->getErrors();

        $expected = [
            ['Field id is not a valid absolute integer and greater than 0'],
        ];

        $this->assertSame($expected, $actual);

        $input        = [
            'id'    => 1,
            'email' => 'not-email',
        ];
        $companyInput = $factory->update($input);

        /** @var CompanyValidatorUpdate $validator */
        $validator = $this->container->get(CompanyValidatorUpdate::class);

        $result = $validator->validate($companyInput);
        $actual = $result->getErrors();

        $expected = [
            ['Field email must be an email address'],
        ];
        $this->assertSame($expected, $actual);
    }

    public function testSuccess(): void
    {
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);
        /** @var CompanyValidatorUpdate $validator */
        $validator = $this->container->get(CompanyValidatorUpdate::class);
        $faker     = FakerFactory::create();

        $input = [
            'id'    => $faker->numberBetween(1, 100),
            'name'  => $faker->company(),
            'email' => $faker->safeEmail(),
        ];

        $companyInput = $factory->update($input);

        $result = $validator->validate($companyInput);
        $actual = $result->getErrors();

        $expected = [];
        $this->assertSame($expected, $actual);
    }
}
