<?php

namespace Src\Domain\Tab;

use Codderz\Yoko\Domain\Aggregate;
use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\MarkFoodPrepared;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\ItemNotOutstanding;
use Src\Domain\Tab\Exceptions\PaymentNotEnough;
use Src\Domain\Tab\Exceptions\TabAlreadyClosed;
use Src\Domain\Tab\Exceptions\TabAlreadyOpen;
use Src\Domain\Tab\Exceptions\TabHasOutstandingItems;
use Src\Domain\Tab\Exceptions\TabNotOpen;

class TabAggregate extends Aggregate
{
    private ?bool $open = null;
    private float $servedItemsValue = 0;

    /** @var Collection<OrderedItem> */
    private Collection $outstandingDrinks;
    /** @var Collection<OrderedItem> */
    private Collection $outstandingFood;
    /** @var Collection<OrderedItem> */
    private Collection $preparedFood;

    protected function __construct()
    {
        $this->outstandingDrinks = Collection::make();
        $this->outstandingFood = Collection::make();
        $this->preparedFood = Collection::make();
    }

    public function handleOpenTab(OpenTab $command)
    {
        if ($this->open === true) throw TabAlreadyOpen::new();
        if ($this->open === false) throw TabAlreadyClosed::new();

        return $this->recordThat(
            TabOpened::of($command->id, $command->tableNumber, $command->waiter)
        );
    }

    public function handlePlaceOrder(PlaceOrder $command)
    {
        if (!$this->open) throw TabNotOpen::new();

        $drinks = $command->items
            ->filter(fn(OrderedItem $item) => $item->isDrink)
            ->values();
        if ($drinks->isNotEmpty()) {
            $this->recordThat(DrinksOrdered::of($command->id, $drinks));
        }

        $food = $command->items
            ->filter(fn(OrderedItem $item) => !$item->isDrink)
            ->values();
        if ($food->isNotEmpty()) {
            $this->recordThat(FoodOrdered::of($command->id, $food));
        }
    }

    public function handleMarkDrinksServed(MarkDrinksServed $command)
    {
        if (!$this->hasAllMenuNumbersForItems($this->outstandingDrinks, $command->menuNumbers)) {
            throw ItemNotOutstanding::new();
        }
        return $this->recordThat(
            DrinksServed::of($command->id, $command->menuNumbers)
        );
    }

    public function handleMarkFoodPrepared(MarkFoodPrepared $command)
    {
        if (!$this->hasAllMenuNumbersForItems($this->outstandingFood, $command->menuNumbers)) {
            throw ItemNotOutstanding::new();
        }
        return $this->recordThat(
            FoodPrepared::of($command->id, $command->menuNumbers)
        );
    }

    public function handleCloseTab(CloseTab $command)
    {
        if (!$this->open) throw TabNotOpen::new();
        if ($this->hasOutstandingItems()) throw TabHasOutstandingItems::new();

        $tipValue = $command->amountPaid - $this->servedItemsValue;

        if ($tipValue < 0) throw PaymentNotEnough::new();

        return $this->recordThat(
            TabClosed::of($command->id, $command->amountPaid, $this->servedItemsValue, $tipValue)
        );
    }

    //

    private function hasAllMenuNumbersForItems(Collection $items, Collection $menuNumbers)
    {
        $items = $items->values(); // ???
        foreach ($menuNumbers as $number) {
            /** @var OrderedItem $item */
            if (!$item = $items->first($this->findOrderedItemByNumber($number))) {
                return false;
            }
            $items = $items->removeFirst($item);
        }
        return true;
    }

    private function hasOutstandingItems()
    {
        return $this->outstandingDrinks->count() > 0
            || $this->outstandingFood->count() > 0;
    }

    private function findOrderedItemByNumber(int $number)
    {
        return fn(OrderedItem $item) => $item->menuNumber === $number;
    }

    //

    public function applyTabOpened(TabOpened $event)
    {
        $this->open = true;
    }

    public function applyDrinksOrdered(DrinksOrdered $event)
    {
        foreach ($event->items as $item) {
            $this->outstandingDrinks = $this->outstandingDrinks->add($item);
        }
    }

    public function applyFoodOrdered(FoodOrdered $event)
    {
        foreach ($event->items as $item) {
            $this->outstandingFood = $this->outstandingFood->add($item);
        }
    }

    public function applyDrinksServed(DrinksServed $event)
    {
        foreach ($event->menuNumbers as $number) {
            /** @var OrderedItem $item */
            $item = $this->outstandingDrinks->first($this->findOrderedItemByNumber($number));
            $this->outstandingDrinks = $this->outstandingDrinks->removeFirst($item);
            $this->servedItemsValue += $item->price;
        }
    }


    public function applyFoodPrepared(FoodPrepared $event)
    {
        foreach ($event->menuNumbers as $number) {
            /** @var OrderedItem $item */
            $item = $this->outstandingFood->first($this->findOrderedItemByNumber($number));
            $this->outstandingFood = $this->outstandingFood->removeFirst($item);
            $this->preparedFood = $this->preparedFood->add($item);
        }
    }

    public function applyTabClosed(TabClosed $event)
    {
        $this->open = false;
    }
}
