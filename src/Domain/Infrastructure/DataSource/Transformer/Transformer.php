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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Transformer;

use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\ValueObjectInterface;

/**
 * Transforms objects to array representations so that they can be passed
 * to the payload
 *
 * @template T of ValueObjectInterface
 * @phpstan-type TItem = array<int|string, mixed>
 */
final class Transformer
{
    /**
     * @param int $recordId
     *
     * @return string[]
     */
    public function delete(int $recordId): array
    {
        return [
            'Record deleted successfully [#' . $recordId . '].',
        ];
    }

    /**
     * Transform a single object.
     *
     * @param T&object{id:int} $object
     *
     * @return array<int, TItem>
     * @phpstan-return array<int, TItem>
     */
    public function get(object $object): array
    {
        return [$object->id => $object->toArray()];
    }

    /**
     * @param iterable<T> $collection
     *
     * @return array<int|string, TItem>
     */
    public function getMany(iterable $collection): array
    {
        $result = [];

        /**
         * @var int|string           $key
         * @var ValueObjectInterface $object
         */
        foreach ($collection as $key => $object) {
            $result[$key] = $object->toArray();
        }

        return $result;
    }
}
