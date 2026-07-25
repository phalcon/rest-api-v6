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

use Phalcon\ADR\Responder\ChainResponder;
use Phalcon\ADR\Responder\FormatResponder;
use Phalcon\ADR\Responder\RedirectResponder;
use Phalcon\ADR\Responder\StatusResponder;
use Phalcon\Contracts\ADR\Payload\Payload;
use Phalcon\Contracts\ADR\Responder\Responder;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;

/**
 * The application's responder chain.
 *
 * StatusResponder sets the HTTP status from the payload status through the
 * mapper, so no status-code logic is written here. FormatResponder renders
 * the body through the envelope formatter. ETagResponder adds the header,
 * which a formatter cannot do because it returns a string.
 *
 * The mapper instance is shared with the formatter so the status line and
 * meta.code always come from the same table.
 */
final class ApiResponder implements Responder
{
    private ChainResponder $chain;

    public function __construct()
    {
        $statusMapper = ApiStatusMapper::create();

        $this->chain = new ChainResponder(
            [
                new StatusResponder($statusMapper),
                new RedirectResponder(),
                new FormatResponder([new EnvelopeJsonFormatter($statusMapper)]),
                new ETagResponder(),
            ]
        );
    }

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
        return $this->chain->__invoke($request, $response, $payload);
    }
}
