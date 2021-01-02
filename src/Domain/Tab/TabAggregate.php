<?php

namespace Src\Domain\Tab;

use Codderz\Yoko\Domain\Aggregate;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\TabNotOpen;

class TabAggregate extends Aggregate
{
    private bool $open = false;

    public function handleOpenTab(OpenTab $command)
    {
        return $this->recordThat(
            TabOpened::of($command->id, $command->tableNumber, $command->waiter)
        );
    }

    public function handlePlaceOrder(PlaceOrder $command)
    {
        if (!$this->open) throw TabNotOpen::of();

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

    //

    public function applyTabOpened(TabOpened $event)
    {
        $this->open = true;
    }

    public function applyDrinksOrdered(DrinksOrdered $event)
    {

    }

    public function applyFoodOrdered(FoodOrdered $event)
    {

    }
}
