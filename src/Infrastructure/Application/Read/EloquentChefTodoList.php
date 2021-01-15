<?php

namespace Src\Infrastructure\Application\Read;

use App\Models\Read\ChefTodoListModel;
use Codderz\Yoko\Layers\Application\Read\ReadModel\ReadModel;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Application\Read\ChefTodoList\TodoListGroup;
use Src\Application\Read\ChefTodoList\TodoListItem;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\OrderedItem;

class EloquentChefTodoList extends ReadModel implements ChefTodoListInterface
{
    public function getTodoList(GetTodoList $query): Collection
    {
        return ChefTodoListModel::query()
            ->orderBy('id')
            ->get()
            ->pipeInto(Collection::class)
            ->groupBy('group_id')
            ->map($this->mapGroupCallback())
            ->values();
    }

    //

    public function mapItemCallback()
    {
        return fn($item) => TodoListItem::of(
            $item->menu_number,
            $item->description,
        );
    }

    public function mapGroupCallback()
    {
        return fn($group) => TodoListGroup::of(
            Guid::of($group[0]->tab_id),
            $group->map($this->mapItemCallback())
        );
    }

    //

    public function applyFoodOrdered(FoodOrdered $event)
    {
        $groupId = Guid::generate();

        $createItem = fn(OrderedItem $item) => ChefTodoListModel::query()
            ->insert([
                'tab_id' => $event->id->value,
                'group_id' => $groupId->value,
                'menu_number' => $item->menuNumber,
                'description' => $item->description,
            ]);

        $event->items->each($createItem);
    }

    public function applyFoodPrepared(FoodPrepared $event)
    {
        ChefTodoListModel::query()
            ->where('tab_id', $event->id->value)
            ->whereIn('menu_number', $event->menuNumbers->toArray())
            ->delete();
    }
}
