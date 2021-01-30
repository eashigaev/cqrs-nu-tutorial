<?php

namespace Src\Domain\Tab;

use Codderz\Yoko\Layers\Domain\Aggregate\Aggregate;
use Codderz\Yoko\Layers\Domain\Guid;
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
use Src\Domain\Tab\Exceptions\TabHasOutstandingItems;
use Src\Domain\Tab\Exceptions\TabNotOpen;

class TabAggregate extends Aggregate
{
    public Guid $id;
    public int $table;

    public ?bool $open = null;
    public float $servedItemsValue = 0;

    /** @var Collection<OrderedItem> */
    public Collection $outstandingDrinks;
    /** @var Collection<OrderedItem> */
    public Collection $outstandingFood;
    /** @var Collection<OrderedItem> */
    public Collection $preparedFood;

    public static function openTab(OpenTab $command)
    {
        return (new self())->recordThat(
            TabOpened::of($command->id, $command->tableNumber, $command->waiter)
        );
    }

    public function placeOrder(PlaceOrder $command)
    {
        if (!$this->open) throw TabNotOpen::new();

        $isDrink = fn(OrderedItem $item) => $item->isDrink;

        $drinks = $command->items->filter($isDrink)->values();
        if ($drinks->isNotEmpty()) {
            $this->recordThat(DrinksOrdered::of($this->id, $drinks));
        }

        $food = $command->items->reject($isDrink)->values();
        if ($food->isNotEmpty()) {
            $this->recordThat(FoodOrdered::of($this->id, $food));
        }

        return $this;
    }

    public function markDrinksServed(MarkDrinksServed $command)
    {
        if (!$this->hasAllMenuNumbers($this->outstandingDrinks, $command->menuNumbers)) {
            throw DrinkNotOutstanding::new();
        }
        return $this->recordThat(
            DrinksServed::of($this->id, $command->menuNumbers)
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
            FoodPrepared::of($this->id, $command->menuNumbers)
        );
    }

    public function markFoodServed(MarkFoodServed $command)
    {
        if (!$this->hasAllMenuNumbers($this->preparedFood, $command->menuNumbers)) {
            throw FoodNotOutstanding::new();
        }
        return $this->recordThat(
            FoodServed::of($this->id, $command->menuNumbers)
        );
    }

    public function closeTab(CloseTab $command)
    {
        if (!$this->open) throw TabNotOpen::new();
        if ($this->hasOutstandingItems()) throw TabHasOutstandingItems::new();

        $tipValue = $command->amountPaid - $this->servedItemsValue;

        if ($tipValue < 0) throw PaymentNotEnough::new();

        return $this->recordThat(
            TabClosed::of($this->id, $command->amountPaid, $this->servedItemsValue, $tipValue)
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
        $this->id = $event->id;
        $this->table = $event->tableNumber;
        $this->open = true;
        $this->outstandingDrinks = Collection::of();
        $this->outstandingFood = Collection::of();
        $this->preparedFood = Collection::of();
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

    //

    public static function fromArray(array $array)
    {
        $self = new self();
        $self->id = Guid::of($array['id']);
        $self->table = $array['table'];
        $self->open = $array['open'];
        $self->servedItemsValue = $array['served_items_value'];
        $self->outstandingDrinks = Collection::of($array['outstanding_drinks'])->mapInto(OrderedItem::class);
        $self->outstandingFood = Collection::of($array['outstanding_food'])->mapInto(OrderedItem::class);
        $self->preparedFood = Collection::of($array['prepared_food'])->mapInto(OrderedItem::class);
        return $self;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value,
            'table' => $this->table,
            'open' => $this->open,
            'served_items_value' => $this->servedItemsValue,
            'outstanding_drinks' => $this->outstandingDrinks->toArray(),
            'outstanding_food' => $this->outstandingFood->toArray(),
            'prepared_food' => $this->preparedFood->toArray()
        ];
    }
}
