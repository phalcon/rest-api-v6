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

use Phalcon\Contracts\ADR\Payload\Payload;
use Phalcon\Contracts\ADR\Responder\Responder;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;

use function is_array;
use function json_encode;
use function sha1;

/**
 * Sets the ETag header.
 *
 * The tag covers the payload content only - data and errors - and
 * deliberately excludes the meta block. meta carries a timestamp that
 * changes on every request, so hashing the rendered body would produce a
 * tag that never matches and a header that is useless for caching.
 *
 * This is a chain link rather than part of the formatter because a
 * Formatter returns a string and has no response to set a header on.
 */
final class ETagResponder implements Responder
{
    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param Payload           $payload
     *
     * @return ResponseInterface
     */
    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        Payload $payload
    ): ResponseInterface {
        $result   = $payload->getResult();
        $messages = $payload->getMessages();

        $content = [
            'data'   => is_array($result) ? $result : [],
            'errors' => is_array($messages) ? $messages : [],
        ];

        $encoded = json_encode($content);
        $encoded = (false === $encoded) ? '' : $encoded;

        $response->setHeader('ETag', sha1($encoded));

        return $response;
    }
}
