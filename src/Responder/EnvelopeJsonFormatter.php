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

namespace Phalcon\Api\Responder;

use Phalcon\ADR\Responder\StatusMapper;
use Phalcon\Api\Domain\Infrastructure\Constants\Dates;
use Phalcon\Contracts\ADR\Payload\Payload;
use Phalcon\Contracts\ADR\Responder\Formatter\Formatter;

use function is_array;
use function is_string;
use function json_encode;
use function ksort;
use function sha1;
use function str_contains;

/**
 * Renders the application's response envelope.
 *
 * The stock JsonFormatter encodes the payload result and nothing else. This
 * application answers with a fixed data/errors/meta shape, where meta carries
 * the HTTP code, a message, a UTC timestamp, and a hash over the timestamp
 * and the encoded content.
 *
 * The status mapper is injected so meta.code and the HTTP status line are
 * derived from the same table and cannot drift.
 *
 * This class renders the body only. The ETag is a header, so it lives in
 * ETagResponder - a formatter returns a string and has no response to set
 * headers on.
 */
final class EnvelopeJsonFormatter implements Formatter
{
    public function __construct(
        private readonly StatusMapper $statusMapper
    ) {
    }

    /**
     * @param string $acceptHeader
     *
     * @return bool
     */
    public function accepts(string $acceptHeader): bool
    {
        return str_contains($acceptHeader, 'application/json')
            || str_contains($acceptHeader, '+json');
    }

    /**
     * @return string
     */
    public function contentType(): string
    {
        return 'application/json';
    }

    /**
     * @param Payload $payload
     *
     * @return string
     */
    public function format(Payload $payload): string
    {
        $result   = $payload->getResult();
        $messages = $payload->getMessages();

        $data   = is_array($result) ? $result : [];
        $errors = is_array($messages) ? $messages : [];

        /** @var string $status */
        $status = $payload->getStatus();

        $extras = $payload->getExtras();
        $ref    = '';

        if (is_array($extras) && isset($extras['ref']) && is_string($extras['ref'])) {
            $ref = $extras['ref'];
        }

        $timestamp = Dates::toUTC();
        $encoded   = json_encode(['data' => $data, 'errors' => $errors]);
        $encoded   = (false === $encoded) ? '' : $encoded;

        $meta = [
            'code'      => $this->statusMapper->toHttpCode($status),
            'hash'      => sha1($timestamp . $encoded),
            'message'   => true === empty($errors) ? 'success' : 'error',
            'timestamp' => $timestamp,
        ];

        /**
         * Only error payloads carry a correlation reference, so successful
         * responses keep the meta shape they have always had.
         */
        if ('' !== $ref) {
            $meta['ref'] = $ref;
            ksort($meta);
        }

        $content = [
            'data'   => $data,
            'errors' => $errors,
            'meta'   => $meta,
        ];

        $json = json_encode($content);

        return (false === $json) ? '' : $json;
    }
}
