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

use Phalcon\ADR\Payload\Payload;
use Phalcon\ADR\Payload\Status;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Responder\ApiErrorResponder;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Contracts\ADR\Payload\Payload as PayloadContract;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

use function json_decode;

final class ApiErrorResponderTest extends AbstractUnitTestCase
{
    public function testCarriesTheCorrelationReference(): void
    {
        $payload = (new Payload())
            ->withStatus(Status::NOT_FOUND)
            ->withResult(['message' => 'Not Found', 'ref' => 'abc123'])
        ;

        $body = json_decode($this->respondTo($payload)->getContent(), true);

        $this->assertSame('abc123', $body['meta']['ref']);
    }

    public function testRendersAnUnauthenticatedFailure(): void
    {
        $payload = (new Payload())
            ->withStatus(Status::NOT_AUTHENTICATED)
            ->withResult(['message' => 'Unauthorized', 'ref' => 'abc123'])
        ;

        $response = $this->respondTo($payload);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame([HttpCodesEnum::AppUnauthorized->error()], $body['errors']);
        $this->assertSame('error', $body['meta']['message']);
    }

    public function testRendersAnUnmappedFailureAsServerError(): void
    {
        $payload = (new Payload())
            ->withStatus(Status::ERROR)
            ->withResult(['message' => 'Internal Server Error', 'ref' => 'abc123'])
        ;

        $body = json_decode($this->respondTo($payload)->getContent(), true);

        /**
         * ApiStatusMapper pins ERROR to 200 until Phase 3, so the key here
         * is 200 rather than 500. That is the preserved behavior, not a bug.
         */
        $this->assertSame([[200 => 'Internal Server Error']], $body['errors']);
        $this->assertSame('error', $body['meta']['message']);
    }

    public function testRendersMethodNotAllowed(): void
    {
        $payload = (new Payload())
            ->withStatus(Status::METHOD_NOT_ALLOWED)
            ->withResult(['message' => 'Method Not Allowed', 'ref' => 'abc123'])
        ;

        $response = $this->respondTo($payload);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame(405, $response->getStatusCode());
        $this->assertSame([], $body['data']);
        $this->assertSame([[405 => 'Method Not Allowed']], $body['errors']);
        $this->assertSame('error', $body['meta']['message']);
    }

    public function testRendersRouteNotFoundInTheApplicationEnvelope(): void
    {
        $payload = (new Payload())
            ->withStatus(Status::NOT_FOUND)
            ->withResult(['message' => 'Not Found', 'ref' => 'abc123'])
        ;

        $response = $this->respondTo($payload);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $body['data']);
        $this->assertSame([HttpCodesEnum::AppResourceNotFound->error()], $body['errors']);
        $this->assertSame('error', $body['meta']['message']);
    }

    private function respondTo(PayloadContract $payload): Response
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $responder = new ApiErrorResponder();
        $response  = new Response();

        $responder->__invoke(new Request(), $response, $payload);

        return $response;
    }
}
