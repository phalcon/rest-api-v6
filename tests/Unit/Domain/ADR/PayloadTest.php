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

use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class PayloadTest extends AbstractUnitTestCase
{
    public function testCreated(): void
    {
        $data    = ['id' => 1];
        $payload = Payload::created($data);

        $expected = Payload::class;
        $actual   = get_class($payload);
        $this->assertSame($expected, $actual);

        $expected = DomainStatus::CREATED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $expected = ['data' => $data];
        $actual   = $payload->getResult();
        $this->assertSame($expected, $actual);
    }

    public function testDeleted(): void
    {
        $data    = ['deleted' => true];
        $payload = Payload::deleted($data);

        $expected = DomainStatus::DELETED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $expected = ['data' => $data];
        $actual   = $payload->getResult();
        $this->assertSame($expected, $actual);
    }

    public function testError(): void
    {
        $errors  = [['something' => 'went wrong']];
        $payload = Payload::error($errors);

        $expected = DomainStatus::ERROR;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $expected = ['errors' => $errors];
        $actual   = $payload->getResult();
        $this->assertSame($expected, $actual);
    }

    public function testInvalid(): void
    {
        $errors  = [['field' => 'invalid']];
        $payload = Payload::invalid($errors);

        $expected = DomainStatus::INVALID;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $expected = ['errors' => $errors];
        $actual   = $payload->getResult();
        $this->assertSame($expected, $actual);
    }

    public function testNotFound(): void
    {
        $payload = Payload::notFound();

        $expected = DomainStatus::NOT_FOUND;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $expected = [
            'code'    => HttpCodesEnum::NotFound->value,
            'message' => HttpCodesEnum::NotFound->text(),
            'data'    => [],
            'errors'  => [['Record(s) not found']],
        ];
        $actual   = $payload->getResult();
        $this->assertSame($expected, $actual);
    }

    public function testSuccess(): void
    {
        $data    = ['items' => [1, 2, 3]];
        $payload = Payload::success($data);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $expected = ['data' => $data];
        $actual   = $payload->getResult();
        $this->assertSame($expected, $actual);
    }

    public function testUnauthorized(): void
    {
        $errors  = [['reason' => 'no access']];
        $payload = Payload::unauthorized($errors);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $expected = [
            'code'    => HttpCodesEnum::Unauthorized->value,
            'message' => HttpCodesEnum::Unauthorized->text(),
            'data'    => [],
            'errors'  => $errors,
        ];
        $actual   = $payload->getResult();
        $this->assertSame($expected, $actual);
    }

    public function testUpdated(): void
    {
        $data    = ['updated' => true];
        $payload = Payload::updated($data);

        $expected = DomainStatus::UPDATED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $expected = ['data' => $data];
        $actual   = $payload->getResult();
        $this->assertSame($expected, $actual);
    }
}
