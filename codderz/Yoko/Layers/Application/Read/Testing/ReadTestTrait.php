<?php

namespace Codderz\Yoko\Layers\Application\Read\Testing;

use Codderz\Yoko\Layers\Application\Read\QueryResult;

trait ReadTestTrait
{
    public function assertResult(QueryResult $result, array $sample)
    {
        $this->assertEquals($result->toArray(), $sample);
    }
}
