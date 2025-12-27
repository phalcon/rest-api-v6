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
use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Infrastructure\Constants\Dates;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Http\ResponseInterface;

use function sha1;

/**
 * @phpstan-import-type TData from ResponderTypes
 * @phpstan-import-type TErrors from ResponderTypes
 * @phpstan-import-type TResult from ResponderTypes
 * @phpstan-import-type TResponsePayload from ResponderTypes
 */
final class JsonResponder implements ResponderInterface
{
    /**
     * @param ResponseInterface $response
     * @param Payload           $payload
     *
     * @return ResponseInterface
     * @throws BaseException
     */
    public function __invoke(
        ResponseInterface $response,
        Payload $payload
    ): ResponseInterface {
        $result = $payload->getResult();

        /**
         * Check the code, message, data and errors
         */
        /** @var int $code */
        $code = $result['code'] ?? HttpCodesEnum::OK->value;
        /** @var string $message */
        $message = $result['message'] ?? HttpCodesEnum::OK->text();
        /** @var TData $data */
        $data = $result['data'] ?? [];
        /** @var TErrors $errors */
        $errors        = $result['errors'] ?? [];
        $statusMessage = true === empty($errors) ? 'success' : 'error';

        $content = [
            'data'   => $data,
            'errors' => $errors,
            'meta'   => [
                'code'      => $code,
                'hash'      => '',
                'message'   => $statusMessage,
                'timestamp' => '',
            ],
        ];

        [$content, $eTag] = $this->calculateMeta($content);

        $response
            ->setHeader('ETag', $eTag)
            ->setStatusCode($code, $message)
            ->setJsonContent($content)
            ->send()
        ;

        return $response;
    }

    /**
     * @param TResponsePayload $content
     *
     * @return array{0: TResponsePayload, 1: string}
     */
    private function calculateMeta(array $content): array
    {
        $payload   = [
            'data'   => $content['data'],
            'errors' => $content['errors'],
        ];
        $encoded   = json_encode($payload);
        $encoded   = (false === $encoded) ? '' : $encoded;
        $timestamp = Dates::toUTC();
        $hash      = sha1($timestamp . $encoded);
        $eTag      = sha1($encoded);

        $content['meta']['timestamp'] = $timestamp;
        $content['meta']['hash']      = $hash;


        return [$content, $eTag];
    }
}
