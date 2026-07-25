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

namespace Phalcon\Api\Tests\Unit\Responder;

use Phalcon\ADR\Payload\Status;
use Phalcon\ADR\Responder\StatusMapper;
use Phalcon\Api\Responder\ApiStatusMapper;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class ApiStatusMapperTest extends AbstractUnitTestCase
{
    public function testCreatesAStatusMapper(): void
    {
        $this->assertInstanceOf(StatusMapper::class, ApiStatusMapper::create());
    }

    public function testKeepsTheAdrDefaultsEverywhereElse(): void
    {
        $mapper = ApiStatusMapper::create();

        $this->assertSame(200, $mapper->toHttpCode(Status::SUCCESS));
        $this->assertSame(200, $mapper->toHttpCode(Status::UPDATED));
        $this->assertSame(200, $mapper->toHttpCode(Status::DELETED));
        $this->assertSame(404, $mapper->toHttpCode(Status::NOT_FOUND));
        $this->assertSame(401, $mapper->toHttpCode(Status::NOT_AUTHENTICATED));
    }

    public function testPreservesTodaysCodesForTheOverriddenStatuses(): void
    {
        $mapper = ApiStatusMapper::create();

        /**
         * These three differ from the ADR defaults on purpose - see the
         * class docblock. Phase 3 changes them; until then they are locked.
         */
        $this->assertSame(200, $mapper->toHttpCode(Status::CREATED));
        $this->assertSame(200, $mapper->toHttpCode(Status::ERROR));
        $this->assertSame(200, $mapper->toHttpCode(Status::NOT_VALID));
    }
}
