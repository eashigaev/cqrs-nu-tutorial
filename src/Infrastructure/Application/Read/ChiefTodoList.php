<?php

namespace Src\Infrastructure\Application\Read;

use Codderz\Yoko\Layers\Application\Read\ReadModel;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
use Src\Application\Read\ChiefTodoList\ChiefTodoListInterface;
use Src\Application\Read\ChiefTodoList\Payloads\TodoListGroup;
use Src\Application\Read\ChiefTodoList\Payloads\TodoListItem;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoList;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoListResult;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;

class ChiefTodoList extends ReadModel implements ChiefTodoListInterface
{
    public function handleGetTodoList(GetTodoList $query): GetTodoListResult
    {
        return GetTodoListResult::of(
            Collection::make([
                TodoListGroup::of(Guid::of('tab-123'), Collection::make([
                    TodoListItem::of(16, 'Beef Noodles'),
                    TodoListItem::of(25, 'Vegetable Curry'),
                ])),
                TodoListGroup::of(Guid::of('tab-456'), Collection::make([
                    TodoListItem::of(16, 'Beef Noodles'),
                ]))
            ])
        );
    }

    public function applyFoodOrdered(FoodOrdered $event)
    {
        // TODO: Implement applyFoodOrdered() method.
    }

    public function applyFoodPrepared(FoodPrepared $event)
    {
        // TODO: Implement applyFoodPrepared() method.
    }
}
