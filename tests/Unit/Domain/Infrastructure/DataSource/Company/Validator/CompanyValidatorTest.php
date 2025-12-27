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
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Validator\CompanyValidator;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class CompanyValidatorTest extends AbstractUnitTestCase
{
    public function testError(): void
    {
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);
        /** @var CompanyValidator $validator */
        $validator = $this->container->get(CompanyValidator::class);

        $input        = [];
        $companyInput = $factory->insert($input);

        $result = $validator->validate($companyInput);
        $actual = $result->getErrors();

        $expected = [
            ['Field name is required'],
            ['Field email is required'],
            ['Field email must be an email address'],
        ];

        $this->assertSame($expected, $actual);
    }

    public function testSuccess(): void
    {
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);
        /** @var CompanyValidator $validator */
        $validator = $this->container->get(CompanyValidator::class);
        $faker     = FakerFactory::create();

        $input = [
            'name'  => $faker->company(),
            'email' => $faker->safeEmail(),
        ];

        $companyInput = $factory->insert($input);

        $result = $validator->validate($companyInput);
        $actual = $result->getErrors();

        $expected = [];
        $this->assertSame($expected, $actual);
    }
}
