<?php

namespace Src\Infrastructure\Application\Read;

use App\Models\Read\OpenTabsItemModel;
use App\Models\Read\OpenTabsTabModel;
use Codderz\Yoko\Layers\Application\Read\ReadModel;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
use Src\Application\Read\OpenTabs\Exceptions\OpenTabNotFound;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Application\Read\OpenTabs\Queries\GetInvoiceForTable;
use Src\Application\Read\OpenTabs\TabInvoice;
use Src\Application\Read\OpenTabs\TabItem;
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
        $items = $tab->items->pipeInto(Collection::make());

        $served = $items
            ->filter(fn($item) => $item->status === OpenTabsItemModel::SERVED_STATUS)
            ->map($this->fnMapTabItem())
            ->values();

        return TabInvoice::of(
            Guid::of($tab->tab_id),
            $tab->table_number,
            $served,
            $served->sum(fn(TabItem $item) => $item->price),
            $served->count() !== $items->count()
        );
    }

    //

    public function fnMapTabItem()
    {
        return fn($item) => TabItem::of(
            $item->menu_number,
            $item->description,
            $item->price
        );
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
        $fn = $this->fnInsertTabItem($event->id->value, OpenTabsItemModel::TO_SERVE_STATUS);

        $event->items->each($fn);
    }

    public function applyFoodOrdered(FoodOrdered $event)
    {
        $fn = $this->fnInsertTabItem($event->id->value, OpenTabsItemModel::IN_PREPARATION_STATUS);

        $event->items->each($fn);
    }

    public function applyDrinksServed(DrinksServed $event)
    {
        $fn = $this->fnChangeTabItemStatus($event->id->value, OpenTabsItemModel::SERVED_STATUS);

        $event->menuNumbers->each($fn);
    }

    public function applyFoodPrepared(FoodPrepared $event)
    {
        $fn = $this->fnChangeTabItemStatus($event->id->value, OpenTabsItemModel::TO_SERVE_STATUS);

        $event->menuNumbers->each($fn);
    }

    public function applyFoodServed(FoodServed $event)
    {
        $fn = $this->fnChangeTabItemStatus($event->id->value, OpenTabsItemModel::SERVED_STATUS);

        $event->menuNumbers->each($fn);
    }

    public function applyTabClosed(TabClosed $event)
    {
        OpenTabsTabModel::query()
            ->where('tab_id', $event->id->value)
            ->limit(1)
            ->delete();
    }

    //

    public function fnInsertTabItem($tabId, $status)
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

    protected function fnChangeTabItemStatus($tabId, $status)
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
