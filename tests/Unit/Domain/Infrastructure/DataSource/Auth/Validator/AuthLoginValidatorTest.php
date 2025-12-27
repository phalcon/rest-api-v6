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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Auth\Validator;

use Faker\Factory as FakerFactory;
use Phalcon\Api\Domain\Application\Auth\Command\AuthCommandFactory;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Validator\AuthLoginValidator;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class AuthLoginValidatorTest extends AbstractUnitTestCase
{
    public function testError(): void
    {
        /** @var AuthLoginValidator $validator */
        $validator = $this->container->get(AuthLoginValidator::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);

        $input       = [];
        $authCommand = $factory->authenticate($input);

        $result = $validator->validate($authCommand);
        $actual = $result->getErrors();

        $expected = [
            HttpCodesEnum::AppIncorrectCredentials->error(),
        ];

        $this->assertSame($expected, $actual);
    }

    public function testSuccess(): void
    {
        /** @var AuthLoginValidator $validator */
        $validator = $this->container->get(AuthLoginValidator::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);
        $faker   = FakerFactory::create();

        $input       = [
            'email'    => $faker->safeEmail(),
            'password' => $faker->password(),
        ];
        $authCommand = $factory->authenticate($input);

        $result = $validator->validate($authCommand);
        $actual = $result->getErrors();

        $expected = [];
        $this->assertSame($expected, $actual);
    }
}
