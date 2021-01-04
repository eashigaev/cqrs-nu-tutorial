<?php

namespace Src\Domain\Tab\Events;

use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;

class FoodPrepared
{
    public Guid $id;
    /** @var Collection<int> */
    public Collection $menuNumbers;

    public static function of(Guid $id, Collection $menuNumbers)
    {
        $self = new self();
        $self->id = $id;
        $self->menuNumbers = $menuNumbers->assertInt();
        return $self;
    }
}
