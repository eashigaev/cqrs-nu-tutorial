<?php

namespace Src\Application\Read\ChiefTodoList\Payloads;

use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;

class TodoListGroup
{
    public Guid $tabId;
    /** @var Collection<TodoListItem> */
    public Collection $items;

    public static function of(Guid $tabId, Collection $items)
    {
        $self = new self;
        $self->tabId = $tabId;
        $self->items = $items->assertInstance(TodoListItem::class);
        return $self;
    }
}
