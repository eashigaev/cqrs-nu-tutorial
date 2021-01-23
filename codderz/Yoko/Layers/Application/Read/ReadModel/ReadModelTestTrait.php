<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Contracts\ArrayableInterface;

trait ReadModelTestTrait
{
    public function assertResult(ArrayableInterface $result, array $sample)
    {
        $this->assertEquals($result->toArray(), $sample);
    }
}
