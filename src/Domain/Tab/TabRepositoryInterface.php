<?php

namespace Src\Domain\Tab;

use Codderz\Yoko\Layers\Domain\Guid;

interface TabRepositoryInterface
{
    public function ofId(Guid $id): ?TabAggregate;

    public function save(TabAggregate $aggregate);
}
