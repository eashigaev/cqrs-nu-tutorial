<?php

namespace Codderz\Yoko\Support;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function assert(callable $callback)
    {
        return $this
            ->filter(function ($item) use ($callback) {
                if (!$callback($item)) {
                    throw new \InvalidArgumentException();
                }
                return $item;
            });
    }

    public function assertType(string $type)
    {
        return $this->assert(fn($item) => is_a($item, $type));
    }

    public function assertInt()
    {
        return $this->assert(fn($item) => is_int($item));
    }

    public function remove($item)
    {
        return $this->reject(fn($elem) => $elem === $item);
    }
}
