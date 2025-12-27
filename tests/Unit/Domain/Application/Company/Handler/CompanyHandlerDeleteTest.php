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

namespace Phalcon\Api\Tests\Unit\Domain\Application\Company\Handler;

use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactory;
use Phalcon\Api\Domain\Application\Company\Handler\CompanyDeleteHandler;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\CompaniesMigration;

final class CompanyHandlerDeleteTest extends AbstractUnitTestCase
{
    public function testHandlerWithCompanyId(): void
    {
        /** @var CompanyDeleteHandler $handler */
        $handler = $this->container->get(CompanyDeleteHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);

        /**
         * We need to ask for a company to be deleted with an ID that does not
         * exist in the database. To ensure that, we will create a company,
         * delete it and then try to delete the same company with that ID
         */
        $migration = new CompaniesMigration($this->getConnection());
        $dbCompany = $this->getNewCompany($migration);
        $companyId = $dbCompany['com_id'];

        $command = $factory->delete(['id' => $companyId]);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::DELETED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $expected = [
            'Record deleted successfully [#' . $companyId . '].',
        ];
        $actual   = $data;
        $this->assertSame($expected, $actual);

        /**
         * Now deleting it again, will result in a 404
         */
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::NOT_FOUND;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [['Record(s) not found']];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testHandlerZeroCompanyId(): void
    {
        /** @var CompanyDeleteHandler $handler */
        $handler = $this->container->get(CompanyDeleteHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);

        $command = $factory->delete(['id' => 0]);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::NOT_FOUND;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [['Record(s) not found']];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }
}
