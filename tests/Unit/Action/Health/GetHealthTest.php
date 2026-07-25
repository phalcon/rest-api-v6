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

namespace Phalcon\Api\Tests\Unit\Action\Health;

use Phalcon\Api\Action\Health\GetHealth;
use Phalcon\Api\Responder\ApiResponder;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

use function json_decode;

final class GetHealthTest extends AbstractUnitTestCase
{
    public function testAnswersTheSamePayloadTheMiddlewareDid(): void
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $response = new Response();
        $action   = new GetHealth(new ApiResponder(), $response);

        $action->__invoke(new Request());

        $body = json_decode($response->getContent(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('ok', $body['data']['status']);
        $this->assertSame('service operational', $body['data']['message']);
        $this->assertSame([], $body['errors']);
        $this->assertSame('success', $body['meta']['message']);
    }
}
