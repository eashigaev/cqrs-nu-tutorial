<?php

namespace Codderz\Yoko\Layers\Application\Read\Testing;

use Codderz\Yoko\Contracts\ArrayableInterface;

trait ReadTestTrait
{
    public function assertResult(ArrayableInterface $result, array $sample)
    {
        $this->assertEquals($result->toArray(), $sample);
    }
}
