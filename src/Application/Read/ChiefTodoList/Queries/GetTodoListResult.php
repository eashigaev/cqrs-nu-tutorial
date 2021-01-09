<?php

namespace Src\Application\Read\ChiefTodoList\Queries;

use Codderz\Yoko\Layers\Application\Read\QueryResult;
use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChiefTodoList\Payloads\TodoListGroup;
use Src\Application\Read\ChiefTodoList\Payloads\TodoListItem;

class GetTodoListResult extends QueryResult
{
    /** @var Collection<TodoListGroup> */
    public Collection $items;

    public static function of(Collection $items)
    {
        $self = new self();
        $self->items = $items;
        return $self;
    }

    public function toArray(): array
    {
        $mapItem = fn(TodoListItem $item) => [
            'menuNumber' => $item->menuNumber,
            'description' => $item->description
        ];

        $mapGroup = fn(TodoListGroup $group) => [
            'tabId' => $group->tabId->value,
            'items' => $group->items->map($mapItem)->toArray()
        ];

        return $this->items
            ->map($mapGroup)
            ->toArray();
    }
}
