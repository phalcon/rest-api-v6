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

namespace Phalcon\Api\Domain\Infrastructure\DataSource;

use Countable;
use Generator;
use IteratorAggregate;

use function count;

/**
 * Collection for domain objects
 *
 * @template T of object
 * @implements IteratorAggregate<int, T>
 */
abstract class AbstractCollection implements IteratorAggregate, Countable
{
    /** @var array<int, T> */
    private array $items = [];

    /**
     * Add an item to the collection.
     *
     * @param T&object{id:int} $item
     */
    public function add(object $item): void
    {
        $this->items[$item->id] = $item;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return Generator<int|string, T>
     */
    public function getIterator(): Generator
    {
        foreach ($this->items as $key => $item) {
            yield $key => $item;
        }
    }

    /**
     * @return T[]
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
