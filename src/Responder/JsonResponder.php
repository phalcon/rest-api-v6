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

use Exception as BaseException;
use Phalcon\Contracts\ADR\Payload\Payload as PayloadContract;
use Phalcon\Http\ResponseInterface;

use function is_array;
use function json_encode;
use function sha1;

/**
 * The responder the Micro application still uses.
 *
 * Since the move to the native payload, the body and the status code are
 * built by EnvelopeJsonFormatter and ApiStatusMapper, so this class is a
 * thin adapter between the old ResponderInterface signature and the new
 * pieces. Phase 2B replaces it with ApiResponder and deletes it.
 */
final class JsonResponder implements ResponderInterface
{
    /**
     * @param ResponseInterface $response
     * @param PayloadContract   $payload
     *
     * @return ResponseInterface
     * @throws BaseException
     */
    public function __invoke(
        ResponseInterface $response,
        PayloadContract $payload
    ): ResponseInterface {
        $statusMapper = ApiStatusMapper::create();
        $formatter    = new EnvelopeJsonFormatter($statusMapper);

        /** @var string $status */
        $status = $payload->getStatus();

        $response
            ->setHeader('ETag', $this->calculateETag($payload))
            ->setStatusCode($statusMapper->toHttpCode($status))
            ->setContentType($formatter->contentType())
            ->setContent($formatter->format($payload))
            ->send()
        ;

        return $response;
    }

    /**
     * Content only - data and errors. The meta block carries a timestamp
     * that changes every request, so including it would make the tag
     * useless for caching.
     *
     * @param PayloadContract $payload
     *
     * @return string
     */
    private function calculateETag(PayloadContract $payload): string
    {
        $result   = $payload->getResult();
        $messages = $payload->getMessages();

        $encoded = json_encode(
            [
                'data'   => is_array($result) ? $result : [],
                'errors' => is_array($messages) ? $messages : [],
            ]
        );

        return sha1((false === $encoded) ? '' : $encoded);
    }
}
