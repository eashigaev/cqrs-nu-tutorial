<?php

namespace Src\Infrastructure\Application\Read;

use App\Models\Read\OpenTabsItemModel;
use App\Models\Read\OpenTabsTabModel;
use Codderz\Yoko\Layers\Application\Read\ReadModel\ReadModel;
use Codderz\Yoko\Layers\Domain\Guid;
use Codderz\Yoko\Support\Collection;
use Src\Application\Read\OpenTabs\Exceptions\OpenTabNotFound;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Application\Read\OpenTabs\Queries\GetInvoiceForTable;
use Src\Application\Read\OpenTabs\Queries\GetTabForTable;
use Src\Application\Read\OpenTabs\Queries\GetTodoListForWaiter;
use Src\Application\Read\OpenTabs\TabInvoice;
use Src\Application\Read\OpenTabs\TabItem;
use Src\Application\Read\OpenTabs\TabStatus;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\OrderedItem;

class EloquentOpenTabs extends ReadModel implements OpenTabsInterface
{
    /* @return Collection<int> */
    public function getActiveTableNumbers(GetActiveTableNumbers $query): Collection
    {
        return OpenTabsTabModel::query()
            ->get()
            ->pipeInto(Collection::class)
            ->map(fn($item) => $item->table_number)
            ->values();
    }

    public function getInvoiceForTable(GetInvoiceForTable $query): TabInvoice
    {
        $tab = OpenTabsTabModel::query()
            ->with('items')
            ->where('table_number', $query->table)
            ->first();

        if (!$tab) throw OpenTabNotFound::new();

        /** @var Collection $items */
        $items = $tab->items->pipeInto(Collection::of());

        $served = $this->filterItemsOfStatus($items, OpenTabsItemModel::SERVED_STATUS);

        return TabInvoice::of(
            Guid::of($tab->tab_id),
            $tab->table_number,
            $served,
            $served->sum(fn(TabItem $item) => $item->price),
            $served->count() !== $items->count()
        );
    }


    public function getTabForTable(GetTabForTable $query): TabStatus
    {
        $tab = OpenTabsTabModel::query()
            ->with('items')
            ->where('table_number', $query->table)
            ->first();

        if (!$tab) throw OpenTabNotFound::new();

        /** @var Collection $items */
        $items = $tab->items->pipeInto(Collection::of());

        return TabStatus::of(
            $tab->table_number,
            $tab->waiter,
            $this->filterItemsOfStatus($items, OpenTabsItemModel::TO_SERVE_STATUS),
            $this->filterItemsOfStatus($items, OpenTabsItemModel::IN_PREPARATION_STATUS),
            $this->filterItemsOfStatus($items, OpenTabsItemModel::SERVED_STATUS),
        );
    }

    /* @return Collection<int, Collection<TabItem>> */
    public function getTodoListForWaiter(GetTodoListForWaiter $query): Collection
    {
        $mapTodoCallback = fn($tab) => [
            $tab->table_number => $this->filterItemsOfStatus(
                $tab->items->pipeInto(Collection::class), OpenTabsItemModel::TO_SERVE_STATUS
            )
        ];

        $rejectEmptyTodoCallback = fn(Collection $tab, $table) => $tab->isEmpty();

        return OpenTabsTabModel::query()
            ->with('items')
            ->where('waiter', $query->waiter)
            ->get()
            ->pipeInto(Collection::class)
            ->mapWithKeys($mapTodoCallback)
            ->reject($rejectEmptyTodoCallback);
    }

    //

    /** @return Collection<TabItem> */
    protected function filterItemsOfStatus(Collection $items, $status): Collection
    {
        return $items
            ->filter(fn($item) => $item->status == $status)
            ->map(fn($item) => TabItem::of(
                $item->menu_number,
                $item->description,
                $item->price
            ))
            ->values();
    }

    //

    public function applyTabOpened(TabOpened $event)
    {
        OpenTabsTabModel::query()
            ->insert([
                'tab_id' => $event->id->value,
                'table_number' => $event->tableNumber,
                'waiter' => $event->waiter
            ]);
    }

    public function applyDrinksOrdered(DrinksOrdered $event)
    {
        $event->items->each(
            $this->insertItemCallback($event->id->value, OpenTabsItemModel::TO_SERVE_STATUS)
        );
    }

    public function applyFoodOrdered(FoodOrdered $event)
    {
        $event->items->each(
            $this->insertItemCallback($event->id->value, OpenTabsItemModel::IN_PREPARATION_STATUS)
        );
    }

    public function applyDrinksServed(DrinksServed $event)
    {
        $event->menuNumbers->each(
            $this->updateItemStatusCallback($event->id->value, OpenTabsItemModel::SERVED_STATUS)
        );
    }

    public function applyFoodPrepared(FoodPrepared $event)
    {
        $event->menuNumbers->each(
            $this->updateItemStatusCallback($event->id->value, OpenTabsItemModel::TO_SERVE_STATUS)
        );
    }

    public function applyFoodServed(FoodServed $event)
    {
        $event->menuNumbers->each(
            $this->updateItemStatusCallback($event->id->value, OpenTabsItemModel::SERVED_STATUS)
        );
    }

    public function applyTabClosed(TabClosed $event)
    {
        OpenTabsTabModel::query()
            ->where('tab_id', $event->id->value)
            ->limit(1)
            ->delete();
    }

    //

    protected function insertItemCallback($tabId, $status)
    {
        return fn(OrderedItem $item) => OpenTabsItemModel::query()
            ->insert([
                'tab_id' => $tabId,
                'menu_number' => $item->menuNumber,
                'description' => $item->description,
                'price' => $item->price,
                'status' => $status,
            ]);
    }

    protected function updateItemStatusCallback($tabId, $status)
    {
        return fn(int $menuNumber) => OpenTabsItemModel::query()
            ->where([
                'tab_id' => $tabId,
                'menu_number' => $menuNumber
            ])
            ->update([
                'status' => $status
            ]);
    }
}
