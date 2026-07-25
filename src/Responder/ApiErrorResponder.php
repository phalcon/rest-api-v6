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

use Phalcon\ADR\Payload\Payload;
use Phalcon\ADR\Payload\Status;
use Phalcon\ADR\Responder\StatusMapper;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Contracts\ADR\Payload\Payload as PayloadContract;
use Phalcon\Contracts\ADR\Responder\Responder;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;

use function is_array;
use function is_string;

/**
 * Renders framework-level failures in the application's envelope.
 *
 * ErrorResponder is final, so it is configured rather than extended: it
 * accepts a Responder as its chain, and that is this class. It hands over a
 * payload whose details sit in the result, which would render them as data
 * with an empty errors list - and since the envelope derives meta.message
 * from whether errors is empty, a 404 would report "success".
 *
 * This translates that payload into the shape the application has always
 * answered with, and carries ErrorResponder's correlation reference through
 * to meta.ref so a reported error can be found in the log.
 */
final class ApiErrorResponder implements Responder
{
    private ApiResponder $responder;
    private StatusMapper $statusMapper;

    public function __construct()
    {
        $this->statusMapper = ApiStatusMapper::create();
        $this->responder    = new ApiResponder();
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param PayloadContract   $payload
     *
     * @return ResponseInterface
     */
    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        PayloadContract $payload
    ): ResponseInterface {
        /** @var string $status */
        $status = $payload->getStatus();

        $details = $payload->getResult();
        $ref     = '';

        if (is_array($details) && isset($details['ref']) && is_string($details['ref'])) {
            $ref = $details['ref'];
        }

        $translated = (new Payload())
            ->withStatus($status)
            ->withMessages([$this->errorFor($status)])
        ;

        if ('' !== $ref) {
            $translated = $translated->withExtras(['ref' => $ref]);
        }

        return ($this->responder)($request, $response, $translated);
    }

    /**
     * The application's error entry for a framework status.
     *
     * HttpCodesEnum has an entry for the two statuses the application
     * already answers with. It has none for a method mismatch or a generic
     * failure, and a case cannot simply be added: CS Fixer alphabetizes enum
     * cases, and the sanitizer and validator enums emit results in
     * declaration order, so inserting one reorders unrelated output. Those
     * statuses therefore key on the HTTP code, which keeps the established
     * {code: message} shape without touching the enum.
     *
     * @param string $status
     *
     * @return array<array-key, string>
     */
    private function errorFor(string $status): array
    {
        return match ($status) {
            Status::NOT_FOUND         => HttpCodesEnum::AppResourceNotFound->error(),
            Status::NOT_AUTHENTICATED => HttpCodesEnum::AppUnauthorized->error(),
            default                   => [
                $this->statusMapper->toHttpCode($status) => $this->reasonFor($status),
            ],
        };
    }

    /**
     * @param string $status
     *
     * @return string
     */
    private function reasonFor(string $status): string
    {
        return match ($status) {
            Status::METHOD_NOT_ALLOWED => 'Method Not Allowed',
            default                    => 'Internal Server Error',
        };
    }
}
