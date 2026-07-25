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

namespace Phalcon\Api\Tests\Unit\Action;

use Phalcon\ADR\Exceptions\RouteNotFound;
use Phalcon\ADR\Router\AttributeFilter;
use Phalcon\Api\Action\AbstractAction;
use Phalcon\Api\Action\Auth\PostAuthLogin;
use Phalcon\Api\Action\Auth\PostAuthLogout;
use Phalcon\Api\Action\Auth\PostAuthRefresh;
use Phalcon\Api\Action\Company\DeleteCompany;
use Phalcon\Api\Action\Company\GetCompany;
use Phalcon\Api\Action\Company\GetCompanyAll;
use Phalcon\Api\Action\Company\PostCompany;
use Phalcon\Api\Action\Company\PutCompany;
use Phalcon\Api\Action\User\DeleteUser;
use Phalcon\Api\Action\User\GetUser;
use Phalcon\Api\Action\User\PostUser;
use Phalcon\Api\Action\User\PutUser;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Contracts\ADR\Action;

use function class_exists;

/**
 * The action class names are what the convention router resolves against,
 * so renaming any of them silently changes a URL. These tests pin the names
 * and the id-casting rules.
 */
final class ActionRoutingTest extends AbstractUnitTestCase
{
    public function testCollectionActionsTakeNoParameters(): void
    {
        $filter = new AttributeFilter();

        $noParams = [
            GetCompanyAll::class,
            PostCompany::class,
            PostUser::class,
            PostAuthLogin::class,
            PostAuthLogout::class,
            PostAuthRefresh::class,
        ];

        foreach ($noParams as $class) {
            $this->assertSame([], $filter->filter($class, []), $class . ' declared params');
        }
    }

    public function testEveryActionExistsUnderItsRoutableName(): void
    {
        $expected = [
            PostAuthLogin::class,
            PostAuthLogout::class,
            PostAuthRefresh::class,
            DeleteCompany::class,
            GetCompany::class,
            GetCompanyAll::class,
            PostCompany::class,
            PutCompany::class,
            DeleteUser::class,
            GetUser::class,
            PostUser::class,
            PutUser::class,
        ];

        foreach ($expected as $class) {
            $this->assertTrue(class_exists($class), $class . ' is missing');

            $action = $this->mockWithoutConstructor($class);

            $this->assertInstanceOf(AbstractAction::class, $action);
            $this->assertInstanceOf(Action::class, $action);
        }
    }

    public function testIdBearingActionsCastTheSegmentToAnInteger(): void
    {
        $filter = new AttributeFilter();

        $withId = [
            DeleteCompany::class,
            GetCompany::class,
            PutCompany::class,
            DeleteUser::class,
            GetUser::class,
            PutUser::class,
        ];

        foreach ($withId as $class) {
            $this->assertSame(
                ['id' => 42],
                $filter->filter($class, ['42']),
                $class . ' did not map its id'
            );
        }
    }

    public function testNonNumericIdIsRejectedBeforeDispatch(): void
    {
        $filter = new AttributeFilter();

        $this->expectException(RouteNotFound::class);

        $filter->filter(GetCompany::class, ['abc']);
    }
}
