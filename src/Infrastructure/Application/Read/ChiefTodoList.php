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
use Src\Application\Read\ChiefTodoList\Queries\GetTodoListResult;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\OrderedItem;

class ChiefTodoList extends ReadModel implements ChiefTodoListInterface
{
    public function handleGetTodoList(GetTodoList $query): GetTodoListResult
    {
        $groups = ChiefTodoListModel::query()
            ->orderBy('id')
            ->get()
            ->groupBy('group_id')
            ->map($this->mapGroup())
            ->values();

        return GetTodoListResult::of(
            Collection::fromBase($groups)
        );
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
            Collection::fromBase($group->map($this->mapItem()))
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
