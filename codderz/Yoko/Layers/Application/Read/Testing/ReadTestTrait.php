<?php

namespace Codderz\Yoko\Layers\Application\Read\Testing;

use Codderz\Yoko\Contracts\Arrayable;

trait ReadTestTrait
{
    public function assertResult(Arrayable $result, array $sample)
    {
        $this->assertEquals($result->toArray(), $sample);
    }
}
