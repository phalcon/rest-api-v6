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

namespace Phalcon\Api\Tests\Unit\Domain\ADR;

use Phalcon\ADR\Payload\Payload;
use Phalcon\ADR\Payload\Status;
use Phalcon\Api\Responder\ApiStatusMapper;
use Phalcon\Api\Tests\AbstractUnitTestCase;

/**
 * The application used to ship its own Payload, because the payload Phalcon
 * offered at the time had a single result slot and could not separate data
 * from errors. The native ADR payload has both, so that class is gone.
 *
 * These tests pin the semantics the rest of the application now relies on:
 * data arrives through getResult(), errors through getMessages(), and each
 * factory carries the status the responder maps to an HTTP code.
 */
final class PayloadTest extends AbstractUnitTestCase
{
    public function testDataCarryingFactories(): void
    {
        $data = ['id' => 1];

        $expected = [
            Status::CREATED => Payload::created($data),
            Status::DELETED => Payload::deleted($data),
            Status::SUCCESS => Payload::success($data),
            Status::UPDATED => Payload::updated($data),
        ];

        foreach ($expected as $status => $payload) {
            $this->assertSame($status, $payload->getStatus());
            $this->assertSame($data, $payload->getResult());
            $this->assertNull($payload->getMessages());
        }
    }

    public function testErrorCarryingFactories(): void
    {
        $errors = [['Field email is required']];

        $expected = [
            Status::ERROR             => Payload::error($errors),
            Status::NOT_VALID         => Payload::invalid($errors),
            Status::NOT_FOUND         => Payload::notFound($errors),
            Status::NOT_AUTHENTICATED => Payload::unauthenticated($errors),
        ];

        foreach ($expected as $status => $payload) {
            $this->assertSame($status, $payload->getStatus());
            $this->assertSame($errors, $payload->getMessages());
            $this->assertNull($payload->getResult());
        }
    }

    public function testTheStatusesMapToTheCodesTheApiPromises(): void
    {
        $mapper = ApiStatusMapper::create();

        $expected = [
            200 => Payload::success([]),
            401 => Payload::unauthenticated([]),
            404 => Payload::notFound([]),
        ];

        foreach ($expected as $code => $payload) {
            $this->assertSame($code, $mapper->toHttpCode((string) $payload->getStatus()));
        }
    }

    public function testWithersReturnNewInstances(): void
    {
        $payload = Payload::success(['id' => 1]);
        $changed = $payload->withStatus(Status::UPDATED);

        $this->assertNotSame($payload, $changed);
        $this->assertSame(Status::SUCCESS, $payload->getStatus());
        $this->assertSame(Status::UPDATED, $changed->getStatus());
    }
}
