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
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Validator\UserValidatorUpdate;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class UserValidatorUpdateTest extends AbstractUnitTestCase
{
    public function testError(): void
    {
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);
        /** @var UserValidatorUpdate $validator */
        $validator = $this->container->get(UserValidatorUpdate::class);

        $input     = [];
        $userInput = $factory->update($input);

        $result = $validator->validate($userInput);
        $actual = $result->getErrors();

        $expected = [
            ['Field id is not a valid absolute integer and greater than 0'],
        ];

        $this->assertSame($expected, $actual);

        $input     = [
            'id'    => 1,
            'email' => 'not-email',
        ];
        $userInput = $factory->update($input);

        /** @var UserValidatorUpdate $validator */
        $validator = $this->container->get(UserValidatorUpdate::class);

        $result = $validator->validate($userInput);
        $actual = $result->getErrors();

        $expected = [
            ['Field email must be an email address'],
        ];
        $this->assertSame($expected, $actual);
    }

    public function testSuccess(): void
    {
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);
        /** @var UserValidatorUpdate $validator */
        $validator = $this->container->get(UserValidatorUpdate::class);
        $faker     = FakerFactory::create();

        $input = [
            'id'            => $faker->numberBetween(1, 100),
            'email'         => $faker->safeEmail(),
            'password'      => $faker->password(),
            'issuer'        => $faker->company(),
            'tokenPassword' => $faker->password(),
            'tokenId'       => $faker->uuid(),
        ];

        $userInput = $factory->update($input);

        $result = $validator->validate($userInput);
        $actual = $result->getErrors();

        $expected = [];
        $this->assertSame($expected, $actual);
    }
}
