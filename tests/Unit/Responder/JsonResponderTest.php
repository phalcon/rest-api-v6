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

use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Responder\JsonResponder;
use Phalcon\Api\Responder\ResponderInterface;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Http\ResponseInterface;
use PHPUnit\Framework\Attributes\BackupGlobals;

use function http_response_code;
use function ob_get_clean;
use function ob_start;
use function uniqid;

#[BackupGlobals(true)]
final class JsonResponderTest extends AbstractUnitTestCase
{
    public function testFailure(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->container->getShared(Container::RESPONSE);
        /** @var ResponderInterface $responder */
        $responder = $this->container->get(JsonResponder::class);

        $errorContent = uniqid('error-');
        $payload      = Payload::unauthorized([[1234 => $errorContent]]);

        ob_start();
        $outputResponse = $responder->__invoke($response, $payload);
        $output         = ob_get_clean();
        $contents       = json_decode($output, true);

        $expected = HttpCodesEnum::Unauthorized->value;
        $actual   = http_response_code();
        $this->assertSame($expected, $actual);

        $expected = ResponseInterface::class;
        $this->assertInstanceOf($expected, $outputResponse);

        $this->assertArrayHasKey('data', $contents);
        $this->assertArrayHasKey('errors', $contents);
        $this->assertArrayHasKey('meta', $contents);

        $data   = $contents['data'];
        $errors = $contents['errors'];
        $meta   = $contents['meta'];

        unset($meta['hash'], $meta['timestamp']);

        $this->assertEmpty($data);

        $expected = [
            [1234 => $errorContent],
        ];
        $actual   = $errors;
        $this->assertSame($expected, $actual);


        $expected = [
            'code'    => HttpCodesEnum::Unauthorized->value,
            'message' => 'error',
        ];
        $this->assertSame($expected, $meta);

        $headers = $outputResponse->getHeaders()->toArray();

        $expected = 'ETag';
        $this->assertArrayHasKey($expected, $headers);
        $expected = 'HTTP/1.1 401 Unauthorized';
        $this->assertArrayHasKey($expected, $headers);
        $expected = 'Status';
        $this->assertArrayHasKey($expected, $headers);
        $expected = 'Content-Type';
        $this->assertArrayHasKey($expected, $headers);

        $expected = '401 Unauthorized';
        $actual   = $headers['Status'];
        $this->assertSame($expected, $actual);

        $expected = 'application/json';
        $actual   = $headers['Content-Type'];
        $this->assertSame($expected, $actual);
    }

    public function testSuccess(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->container->getShared(Container::RESPONSE);
        /** @var ResponderInterface $responder */
        $responder = $this->container->getShared(JsonResponder::class);

        $dataContent = uniqid('data-');
        $payload     = Payload::success([$dataContent]);

        ob_start();
        $outputResponse = $responder->__invoke($response, $payload);
        $content        = ob_get_clean();

        $content = json_decode($content, true);

        $expected = HttpCodesEnum::OK->value;
        $actual   = http_response_code();
        $this->assertSame($expected, $actual);

        $expected = ResponseInterface::class;
        $this->assertInstanceOf($expected, $outputResponse);

        $data   = $content['data'];
        $errors = $content['errors'];
        $meta   = $content['meta'];

        unset($meta['hash'], $meta['timestamp']);

        $expected = [
            $dataContent,
        ];
        $actual   = $data;
        $this->assertSame($expected, $actual);

        $this->assertEmpty($errors);

        $expected = [
            'code'    => HttpCodesEnum::OK->value,
            'message' => 'success',
        ];
        $actual   = $meta;
        $this->assertSame($expected, $actual);

        $headers = $outputResponse->getHeaders()->toArray();

        $expected = 'ETag';
        $this->assertArrayHasKey($expected, $headers);
        $expected = 'HTTP/1.1 200 OK';
        $this->assertArrayHasKey($expected, $headers);
        $expected = 'Status';
        $this->assertArrayHasKey($expected, $headers);
        $expected = 'Content-Type';
        $this->assertArrayHasKey($expected, $headers);

        $expected = '200 OK';
        $actual   = $headers['Status'];
        $this->assertSame($expected, $actual);

        $expected = 'application/json';
        $actual   = $headers['Content-Type'];
        $this->assertSame($expected, $actual);
    }
}
