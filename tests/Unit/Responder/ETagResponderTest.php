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
use Phalcon\Api\Responder\ETagResponder;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Contracts\ADR\Payload\Payload as PayloadContract;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

final class ETagResponderTest extends AbstractUnitTestCase
{
    public function testCoversErrorsAsWellAsData(): void
    {
        $first  = $this->respondTo(Payload::success([]));
        $second = $this->respondTo(Payload::invalid([['Field email is required']]));

        $this->assertNotSame(
            $first->getHeaders()->get('ETag'),
            $second->getHeaders()->get('ETag')
        );
    }

    public function testIsStableForIdenticalContent(): void
    {
        /**
         * The guard against hashing the rendered body: meta.timestamp differs
         * between these two calls, so a body hash would not match here.
         */
        $first  = $this->respondTo(Payload::success(['id' => 1]));
        $second = $this->respondTo(Payload::success(['id' => 1]));

        $this->assertSame(
            $first->getHeaders()->get('ETag'),
            $second->getHeaders()->get('ETag')
        );
    }

    public function testSetsAnETagHeader(): void
    {
        $response = $this->respondTo(Payload::success(['id' => 1]));

        $this->assertNotEmpty($response->getHeaders()->get('ETag'));
    }

    public function testVariesWithContent(): void
    {
        $first  = $this->respondTo(Payload::success(['id' => 1]));
        $second = $this->respondTo(Payload::success(['id' => 2]));

        $this->assertNotSame(
            $first->getHeaders()->get('ETag'),
            $second->getHeaders()->get('ETag')
        );
    }

    private function respondTo(PayloadContract $payload): Response
    {
        $responder = new ETagResponder();
        $response  = new Response();

        $responder->__invoke(new Request(), $response, $payload);

        return $response;
    }
}
