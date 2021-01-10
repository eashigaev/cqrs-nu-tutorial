<?php

namespace Codderz\Yoko\Contracts;

use Illuminate\Contracts\Support\Arrayable as BaseArrayable;

interface Arrayable extends BaseArrayable
{
    /** @return array */
    public function toArray();
}
