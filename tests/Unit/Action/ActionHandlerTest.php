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

namespace Phalcon\Api\Tests\Unit\Action;

use Phalcon\Api\Action\ActionHandler;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Responder\JsonResponder;
use Phalcon\Api\Responder\ResponderInterface;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Application\Service\ServiceFixture;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;
use PHPUnit\Framework\Attributes\BackupGlobals;

use function ob_get_clean;
use function ob_start;
use function uniqid;

#[BackupGlobals(true)]
final class ActionHandlerTest extends AbstractUnitTestCase
{
    public function testInvoke(): void
    {
        /** @var RequestInterface $request */
        $request = $this->container->getShared(Container::REQUEST);
        /** @var ResponseInterface $response */
        $response = $this->container->getShared(Container::RESPONSE);
        /** @var ResponderInterface $responder */
        $responder = $this->container->getShared(JsonResponder::class);

        $getData = [
            'key'   => uniqid('key-'),
            'value' => [
                uniqid('data-'),
            ],
        ];

        $_GET = $getData;

        $service = new ServiceFixture();
        $action  = new ActionHandler(
            $request,
            $response,
            $service,
            $responder
        );

        ob_start();
        $action->__invoke();
        $content = ob_get_clean();

        $content = json_decode($content, true);

        $data   = $content['data'];
        $errors = $content['errors'];

        $this->assertEmpty($errors);

        $expected = $getData;
        $actual   = $data;
        $this->assertSame($expected, $actual);
    }
}
