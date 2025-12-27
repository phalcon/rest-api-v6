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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\User\DTO;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Tests\AbstractUnitTestCase;

use function rand;

final class UserTest extends AbstractUnitTestCase
{
    public function testObject(): void
    {
        $userData           = $this->getNewUserData();
        $userData['usr_id'] = rand(1, 100);

        $user = new User(
            $userData['usr_id'],
            $userData['usr_status_flag'],
            $userData['usr_email'],
            $userData['usr_password'],
            $userData['usr_name_prefix'],
            $userData['usr_name_first'],
            $userData['usr_name_middle'],
            $userData['usr_name_last'],
            $userData['usr_name_suffix'],
            $userData['usr_issuer'],
            $userData['usr_token_password'],
            $userData['usr_token_id'],
            $userData['usr_preferences'],
            $userData['usr_created_date'],
            $userData['usr_created_usr_id'],
            $userData['usr_updated_date'],
            $userData['usr_updated_usr_id'],
        );

        $expected = ($userData['usr_name_last'] ?? '')
            . ', '
            . ($userData['usr_name_first'] ?? '')
            . ' '
            . ($userData['usr_name_middle'] ?? '');
        $actual   = $user->fullName();
        $this->assertSame($expected, $actual);

        $expected = [
            'id'            => $userData['usr_id'],
            'status'        => $userData['usr_status_flag'],
            'email'         => $userData['usr_email'],
            'password'      => $userData['usr_password'],
            'namePrefix'    => $userData['usr_name_prefix'],
            'nameFirst'     => $userData['usr_name_first'],
            'nameMiddle'    => $userData['usr_name_middle'],
            'nameLast'      => $userData['usr_name_last'],
            'nameSuffix'    => $userData['usr_name_suffix'],
            'issuer'        => $userData['usr_issuer'],
            'tokenPassword' => $userData['usr_token_password'],
            'tokenId'       => $userData['usr_token_id'],
            'preferences'   => $userData['usr_preferences'],
            'createdDate'   => $userData['usr_created_date'],
            'createdUserId' => $userData['usr_created_usr_id'],
            'updatedDate'   => $userData['usr_updated_date'],
            'updatedUserId' => $userData['usr_updated_usr_id'],
        ];
        $actual   = $user->toArray();
        $this->assertSame($expected, $actual);
    }
}
