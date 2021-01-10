<?php

namespace Src\Infrastructure\Application\Read;

use App\Models\Read\ChiefTodoListModel;
use Codderz\Yoko\Layers\Application\Read\ReadModel;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
use Src\Application\Read\ChiefTodoList\ChiefTodoListInterface;
use Src\Application\Read\ChiefTodoList\Payloads\TodoListGroup;
use Src\Application\Read\ChiefTodoList\Payloads\TodoListItem;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoList;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\OrderedItem;

class EloquentChiefTodoList extends ReadModel implements ChiefTodoListInterface
{
    public function handleGetTodoList(GetTodoList $query): Collection
    {
        return ChiefTodoListModel::query()
            ->orderBy('id')
            ->get()
            ->pipeInto(Collection::class)
            ->groupBy('group_id')
            ->map($this->mapGroup())
            ->values();
    }

    //

    public function mapItem()
    {
        return fn($item) => TodoListItem::of(
            $item->menu_number,
            $item->description,
        );
    }

    public function mapGroup()
    {
        return fn($group) => TodoListGroup::of(
            Guid::of($group[0]->tab_id),
            $group->map($this->mapItem())
        );
    }

    //

    public function applyFoodOrdered(FoodOrdered $event)
    {
        $groupId = Guid::generate();

        $createItem = fn(OrderedItem $item) => ChiefTodoListModel::query()
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
        ChiefTodoListModel::query()
            ->where('tab_id', $event->id->value)
            ->whereIn('menu_number', $event->menuNumbers->toArray())
            ->delete();
    }
}
