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

namespace Phalcon\Api\Action\Company;

use Phalcon\Api\Action\AbstractAction;

/**
 * PUT /company/{id}
 */
final class PutCompany extends AbstractAction
{
    /**
     * The trailing segment becomes an int named "id". A segment failing the
     * match throws RouteNotFound, so a non-numeric id answers 404 rather
     * than reaching the service with a garbage identifier.
     *
     * @return array<string, array<string, string>>
     */
    public static function params(): array
    {
        return [
            'id' => ['type' => 'int', 'match' => '\\d+'],
        ];
    }
}
