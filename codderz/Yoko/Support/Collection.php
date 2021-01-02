<?php

namespace Codderz\Yoko\Support;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function assertType(string $type)
    {
        return $this->map(function ($item) use ($type) {
            if (!is_a($item, $type)) {
                throw new \InvalidArgumentException();
            }
            return $item;
        });
    }
}
