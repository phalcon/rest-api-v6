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
use Phalcon\Api\Responder\ApiStatusMapper;
use Phalcon\Api\Responder\EnvelopeJsonFormatter;
use Phalcon\Api\Tests\AbstractUnitTestCase;

use function array_keys;
use function json_decode;

final class EnvelopeJsonFormatterTest extends AbstractUnitTestCase
{
    public function testAcceptsJsonMediaTypes(): void
    {
        $formatter = $this->newFormatter();

        $this->assertTrue($formatter->accepts('application/json'));
        $this->assertTrue($formatter->accepts('application/vnd.api+json'));
        $this->assertFalse($formatter->accepts('text/html'));
    }

    public function testContentTypeIsJson(): void
    {
        $this->assertSame('application/json', $this->newFormatter()->contentType());
    }

    public function testEnvelopeKeysMatchTheCurrentResponderShape(): void
    {
        $actual = json_decode($this->newFormatter()->format(Payload::success(['id' => 1])), true);

        $this->assertSame(['data', 'errors', 'meta'], array_keys($actual));
        $this->assertSame(['code', 'hash', 'message', 'timestamp'], array_keys($actual['meta']));
    }

    public function testFormatsErrorEnvelope(): void
    {
        $payload = Payload::notFound([['Record(s) not found']]);

        $actual = json_decode($this->newFormatter()->format($payload), true);

        $this->assertSame([], $actual['data']);
        $this->assertSame([['Record(s) not found']], $actual['errors']);
        $this->assertSame(404, $actual['meta']['code']);
        $this->assertSame('error', $actual['meta']['message']);
    }

    public function testFormatsSuccessEnvelope(): void
    {
        $actual = json_decode($this->newFormatter()->format(Payload::success(['id' => 1])), true);

        $this->assertSame(['id' => 1], $actual['data']);
        $this->assertSame([], $actual['errors']);
        $this->assertSame(200, $actual['meta']['code']);
        $this->assertSame('success', $actual['meta']['message']);
        $this->assertNotEmpty($actual['meta']['hash']);
        $this->assertNotEmpty($actual['meta']['timestamp']);
    }

    public function testHashVariesWithContent(): void
    {
        $formatter = $this->newFormatter();

        $first  = json_decode($formatter->format(Payload::success(['id' => 1])), true);
        $second = json_decode($formatter->format(Payload::success(['id' => 2])), true);

        $this->assertNotSame($first['meta']['hash'], $second['meta']['hash']);
    }

    public function testMetaCodeUsesTheOverriddenMapping(): void
    {
        $payload = Payload::invalid([['Field email is required']]);

        $actual = json_decode($this->newFormatter()->format($payload), true);

        /**
         * 200 rather than the ADR default of 422 - see ApiStatusMapper.
         */
        $this->assertSame(200, $actual['meta']['code']);
    }

    private function newFormatter(): EnvelopeJsonFormatter
    {
        return new EnvelopeJsonFormatter(ApiStatusMapper::create());
    }
}
