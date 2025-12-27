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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\User\Validator;

use Faker\Factory as FakerFactory;
use Phalcon\Api\Domain\Application\User\Command\UserCommandFactory;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Validator\UserValidator;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class UserValidatorTest extends AbstractUnitTestCase
{
    public function testError(): void
    {
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);
        /** @var UserValidator $validator */
        $validator = $this->container->get(UserValidator::class);

        $input     = [];
        $userInput = $factory->insert($input);

        $result = $validator->validate($userInput);
        $actual = $result->getErrors();

        $expected = [
            ['Field email is required'],
            ['Field email must be an email address'],
            ['Field password is required'],
            ['Field issuer is required'],
            ['Field tokenPassword is required'],
            ['Field tokenId is required'],
        ];

        $this->assertSame($expected, $actual);
    }

    public function testSuccess(): void
    {
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);
        /** @var UserValidator $validator */
        $validator = $this->container->get(UserValidator::class);
        $faker     = FakerFactory::create();

        $input = [
            'email'         => $faker->safeEmail(),
            'password'      => $faker->password(),
            'issuer'        => $faker->company(),
            'tokenPassword' => $faker->password(),
            'tokenId'       => $faker->uuid(),
        ];

        $userInput = $factory->insert($input);

        $result = $validator->validate($userInput);
        $actual = $result->getErrors();

        $expected = [];
        $this->assertSame($expected, $actual);
    }
}
