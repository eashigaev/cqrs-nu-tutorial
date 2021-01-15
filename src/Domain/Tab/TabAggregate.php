<?php

namespace Src\Domain\Tab;

use Codderz\Yoko\Layers\Domain\Aggregate;
use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\MarkFoodPrepared;
use Src\Domain\Tab\Commands\MarkFoodServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\DrinkNotOutstanding;
use Src\Domain\Tab\Exceptions\FoodAlreadyPrepared;
use Src\Domain\Tab\Exceptions\FoodNotOutstanding;
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

    public function openTab(OpenTab $command)
    {
        if ($this->open === true) throw TabAlreadyOpen::new();
        if ($this->open === false) throw TabAlreadyClosed::new();

        return $this->recordThat(
            TabOpened::of($command->id, $command->tableNumber, $command->waiter)
        );
    }

    public function placeOrder(PlaceOrder $command)
    {
        if (!$this->open) throw TabNotOpen::new();

        $isDrink = fn(OrderedItem $item) => $item->isDrink;

        $drinks = $command->items->filter($isDrink)->values();
        if ($drinks->isNotEmpty()) {
            $this->recordThat(DrinksOrdered::of($command->id, $drinks));
        }

        $food = $command->items->reject($isDrink)->values();
        if ($food->isNotEmpty()) {
            $this->recordThat(FoodOrdered::of($command->id, $food));
        }
    }

    public function markDrinksServed(MarkDrinksServed $command)
    {
        if (!$this->hasAllMenuNumbers($this->outstandingDrinks, $command->menuNumbers)) {
            throw DrinkNotOutstanding::new();
        }
        return $this->recordThat(
            DrinksServed::of($command->id, $command->menuNumbers)
        );
    }

    public function markFoodPrepared(MarkFoodPrepared $command)
    {
        if ($this->hasAllMenuNumbers($this->preparedFood, $command->menuNumbers)) {
            throw FoodAlreadyPrepared::new();
        } elseif (!$this->hasAllMenuNumbers($this->outstandingFood, $command->menuNumbers)) {
            throw FoodNotOutstanding::new();
        }
        return $this->recordThat(
            FoodPrepared::of($command->id, $command->menuNumbers)
        );
    }

    public function markFoodServed(MarkFoodServed $command)
    {
        if (!$this->hasAllMenuNumbers($this->preparedFood, $command->menuNumbers)) {
            throw FoodNotOutstanding::new();
        }
        return $this->recordThat(
            FoodServed::of($command->id, $command->menuNumbers)
        );
    }

    public function closeTab(CloseTab $command)
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

    private function hasAllMenuNumbers(Collection $items, Collection $menuNumbers)
    {
        $items = $items->values();
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
        return $this->outstandingDrinks->isNotEmpty()
            || $this->outstandingFood->isNotEmpty()
            || $this->preparedFood->isNotEmpty();
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

    public function applyFoodServed(FoodServed $event)
    {
        foreach ($event->menuNumbers as $number) {
            /** @var OrderedItem $item */
            $item = $this->preparedFood->first($this->findOrderedItemByNumber($number));
            $this->preparedFood = $this->preparedFood->removeFirst($item);
            $this->servedItemsValue += $item->price;
        }
    }

    public function applyTabClosed(TabClosed $event)
    {
        $this->open = false;
    }
}
