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
use Phalcon\Api\Responder\ApiResponder;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Contracts\ADR\Payload\Payload as PayloadContract;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

use function json_decode;

final class ApiResponderTest extends AbstractUnitTestCase
{
    public function testMapsNotFoundToFourZeroFour(): void
    {
        $response = $this->respondTo(Payload::notFound([['Record(s) not found']]));

        $body = json_decode($response->getContent(), true);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(404, $body['meta']['code']);
        $this->assertSame([['Record(s) not found']], $body['errors']);
    }

    public function testMapsUnauthenticatedToFourZeroOne(): void
    {
        $response = $this->respondTo(Payload::unauthenticated([['Token not present']]));

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testPreservesTwoHundredForValidationFailures(): void
    {
        $response = $this->respondTo(Payload::invalid([['Field email is required']]));

        /**
         * 200 rather than 422 - preserved deliberately, see ApiStatusMapper.
         */
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testSetsStatusBodyAndETagTogether(): void
    {
        $response = $this->respondTo(Payload::success(['id' => 1]));

        $body = json_decode($response->getContent(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['id' => 1], $body['data']);
        $this->assertNotEmpty($response->getHeaders()->get('ETag'));
    }

    public function testStatusLineAndMetaCodeAgree(): void
    {
        $response = $this->respondTo(Payload::notFound([['Record(s) not found']]));

        $body = json_decode($response->getContent(), true);

        $this->assertSame($response->getStatusCode(), $body['meta']['code']);
    }

    private function respondTo(PayloadContract $payload): Response
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $responder = new ApiResponder();
        $response  = new Response();

        $responder->__invoke(new Request(), $response, $payload);

        return $response;
    }
}
