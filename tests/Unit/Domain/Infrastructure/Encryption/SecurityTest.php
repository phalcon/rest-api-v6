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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Encryption;

use Phalcon\Api\Domain\Infrastructure\Encryption\Security;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class SecurityTest extends AbstractUnitTestCase
{
    public function testSecurity(): void
    {
        /** @var Security $security */
        $security = $this->container->get(Security::class);
        $password = $this->getStrongPassword();

        $hashed = $security->hash($password);

        $actual = $security->verify($password, $hashed);
        $this->assertTrue($actual);
    }
}
