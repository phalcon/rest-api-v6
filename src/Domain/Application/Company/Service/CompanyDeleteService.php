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

namespace Phalcon\Api\Domain\Application\Company\Service;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\ADR\Payload;

/**
 * @phpstan-import-type TCompanyInput from InputTypes
 */
final class CompanyDeleteService extends AbstractCompanyService
{
    /**
     * @param TCompanyInput $input
     *
     * @return Payload
     */
    public function __invoke(array $input): Payload
    {
        return $this->facade->delete($input);
    }
}
