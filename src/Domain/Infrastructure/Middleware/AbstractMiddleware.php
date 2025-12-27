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

namespace Phalcon\Api\Domain\Infrastructure\Middleware;

use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Responder\JsonResponder;
use Phalcon\Api\Responder\ResponderInterface;
use Phalcon\Api\Responder\ResponderTypes;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

use function str_replace;

/**
 * @phpstan-import-type TData from ResponderTypes
 * @phpstan-import-type TErrors from ResponderTypes
 */
abstract class AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @param RequestInterface $request
     * @param EnvManager       $env
     *
     * @return string
     */
    public function getBearerTokenFromHeader(
        RequestInterface $request,
        EnvManager $env
    ): string {
        /**
         * For certain local environments the Authorization header is not
         * recognized, as such this is here to allow a custom one through the
         * environment
         */
        /** @var string $header */
        $header = $env->get('API_HEADER_AUTH', 'Authorization');

        return str_replace('Bearer ', '', $request->getHeader($header));
    }

    /**
     * @param RequestInterface $request
     * @param EnvManager       $env
     *
     * @return bool
     */
    public function isEmptyBearerToken(
        RequestInterface $request,
        EnvManager $env
    ): bool {
        return true === empty($this->getBearerTokenFromHeader($request, $env));
    }

    /**
     * @param Micro   $application
     * @param int     $code
     * @param string  $message
     * @param TData   $data
     * @param TErrors $errors
     *
     * @return void
     */
    protected function halt(
        Micro $application,
        int $code,
        string $message = '',
        array $data = [],
        array $errors = []
    ): void {
        /** @var ResponseInterface $response */
        $response = $application->getSharedService(Container::RESPONSE);
        /** @var ResponderInterface $responder */
        $responder = $application->getSharedService(JsonResponder::class);

        $application->stop();

        $results = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
            'errors'  => $errors,
        ];

        $payload = Payload::success($results);

        $responder->__invoke($response, $payload);
    }
}
