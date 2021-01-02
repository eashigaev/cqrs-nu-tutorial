<?php

namespace Src\Domain\Tab\Events;

use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
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
        $self->items = $items->assertType(OrderedItem::class);
        return $self;
    }
}
