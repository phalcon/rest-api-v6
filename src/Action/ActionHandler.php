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

use Phalcon\Api\Domain\ADR\DomainInterface;
use Phalcon\Api\Domain\ADR\Input;
use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Responder\ResponderInterface;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;

/**
 * @phpstan-import-type TAuthLoginInput from InputTypes
 * @phpstan-import-type TUserInput from InputTypes
 */
final readonly class ActionHandler implements ActionInterface
{
    public function __construct(
        private RequestInterface $request,
        private ResponseInterface $response,
        private DomainInterface $service,
        private ResponderInterface $responder
    ) {
    }

    public function __invoke(): void
    {
        $input = new Input();
        /** @var TAuthLoginInput|TUserInput $data */
        $data = $input->__invoke($this->request);

        $this->responder->__invoke(
            $this->response,
            $this->service->__invoke($data)
        );
    }
}
