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

namespace Phalcon\Api\Domain\Application\Company\Command;

use Phalcon\Api\Domain\ADR\InputTypes;

/**
 * @phpstan-import-type TCompanyInput from InputTypes
 */
interface CompanyCommandFactoryInterface
{
    /**
     * @param TCompanyInput $input
     *
     * @return CompanyDeleteCommand
     */
    public function delete(array $input): CompanyDeleteCommand;

    /**
     * @param TCompanyInput $input
     *
     * @return CompanyGetCommand
     */
    public function get(array $input): CompanyGetCommand;

    /**
     * @param TCompanyInput $input
     *
     * @return CompanyGetManyCommand
     */
    public function getMany(array $input): CompanyGetManyCommand;

    /**
     * @param TCompanyInput $input
     *
     * @return CompanyPostCommand
     */
    public function insert(array $input): CompanyPostCommand;

    /**
     * @param TCompanyInput $input
     *
     * @return CompanyPutCommand
     */
    public function update(array $input): CompanyPutCommand;
}
