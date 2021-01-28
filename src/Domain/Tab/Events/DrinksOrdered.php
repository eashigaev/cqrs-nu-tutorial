<?php

namespace Src\Domain\Tab\Events;

use Codderz\Yoko\Layers\Domain\Guid;
use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\OrderedItem;

class DrinksOrdered
{
    public Guid $id;
    /** @var Collection<OrderedItem> */
    public Collection $items;

    public static function of(Guid $id, Collection $items)
    {
        $self = new self();
        $self->id = $id;
        $self->items = $items->assertInstance(OrderedItem::class);
        return $self;
    }
}
