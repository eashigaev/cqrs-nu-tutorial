<?php

namespace Src\Domain\Tab;

use Codderz\Yoko\Domain\Aggregate;
use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\DrinksNotOutstanding;
use Src\Domain\Tab\Exceptions\TabNotOpen;

class TabAggregate extends Aggregate
{
    private bool $open = false;
    /** @var Collection<int> */
    private Collection $outstandingDrinks;

    protected function __construct()
    {
        $this->outstandingDrinks = Collection::make();
    }

    public function handleOpenTab(OpenTab $command)
    {
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
        if (!$this->areDrinksOutstanding($command->menuNumbers)) {
            throw DrinksNotOutstanding::new();
        }
        return $this->recordThat(
            DrinksServed::of($command->id, $command->menuNumbers)
        );
    }

    public function areDrinksOutstanding(Collection $menuNumbers)
    {
        $curOutstanding = $this->outstandingDrinks->values();
        foreach ($menuNumbers as $number) {
            if (!$curOutstanding->contains($number)) return false;
            $curOutstanding = $curOutstanding->removeFirst($number);
        }
        return true;
    }

    //

    public function applyTabOpened(TabOpened $event)
    {
        $this->open = true;
    }

    public function applyDrinksOrdered(DrinksOrdered $event)
    {
        $addDrink = fn(OrderedItem $item) => $this->outstandingDrinks = $this->outstandingDrinks->add($item->menuNumber);

        $event->items->each($addDrink);
    }

    public function applyFoodOrdered(FoodOrdered $event)
    {

    }

    public function applyDrinksServed(DrinksServed $event)
    {
        $removeDrink = fn($item) => $this->outstandingDrinks = $this->outstandingDrinks->removeFirst($item);

        $event->menuNumbers->each($removeDrink);
    }
}
