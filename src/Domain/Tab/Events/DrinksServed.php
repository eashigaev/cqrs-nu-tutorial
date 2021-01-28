<?php

namespace Src\Domain\Tab\Events;

use Codderz\Yoko\Layers\Domain\Guid;
use Codderz\Yoko\Support\Collection;

class DrinksServed
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
