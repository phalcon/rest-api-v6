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

namespace Phalcon\Api\Action;

use Phalcon\ADR\Input\Input;
use Phalcon\Api\Domain\ADR\DomainInterface;
use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Contracts\ADR\Action;
use Phalcon\Contracts\ADR\Responder\Responder;
use Phalcon\Contracts\Http\AttributeRequest;
use Phalcon\Http\ResponseInterface;

/**
 * Turns a request into a domain call and a response.
 *
 * Input::fromRequest merges query, post, a JSON body and the route
 * attributes, which is the array shape every domain service already takes -
 * so a route parameter such as {id} arrives without any per-action work.
 *
 * The service cannot be autowired: DomainInterface is implemented once, by
 * an abstract, so each concrete action pins its own in the container.
 *
 * @phpstan-import-type TAuthLoginInput from InputTypes
 * @phpstan-import-type TUserInput from InputTypes
 */
abstract class AbstractAction implements Action
{
    public function __construct(
        protected readonly DomainInterface $service,
        protected readonly Responder $responder,
        protected readonly ResponseInterface $response,
    ) {
    }

    /**
     * @param AttributeRequest $request
     *
     * @return ResponseInterface
     */
    public function __invoke(AttributeRequest $request): ResponseInterface
    {
        /** @var TAuthLoginInput|TUserInput $input */
        $input = Input::fromRequest($request)->toArray();

        return ($this->responder)(
            $request,
            $this->response,
            ($this->service)($input)
        );
    }
}
