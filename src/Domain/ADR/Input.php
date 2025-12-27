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

namespace Phalcon\Api\Domain\ADR;

use Phalcon\Http\RequestInterface;

use function array_merge;

/**
 * @phpstan-import-type TRequestQuery from InputTypes
 */
final class Input implements InputInterface
{
    /**
     * @param RequestInterface $request
     *
     * @return TRequestQuery
     */
    public function __invoke(RequestInterface $request): array
    {
        /** @var TRequestQuery $query */
        $query = $request->getQuery();
        /** @var TRequestQuery $post */
        $post = $request->getPost();
        /** @var TRequestQuery $put */
        $put = $request->getPut();

        return array_merge($query, $post, $put);
    }
}
