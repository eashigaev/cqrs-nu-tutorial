<?php

namespace Src\Application\Read\ChefTodoList;

use Codderz\Yoko\Contracts\ArrayableInterface;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;

class TodoListGroup implements ArrayableInterface
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

    public function toArray(): array
    {
        return [
            'tabId' => $this->tabId->value,
            'items' => $this->items->toArray()
        ];
    }
}
