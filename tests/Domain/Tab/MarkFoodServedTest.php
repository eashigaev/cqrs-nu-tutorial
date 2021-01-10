<?php

namespace Tests\Domain\Tab;

use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\MarkFoodServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\FoodNotOutstanding;
use Src\Domain\Tab\TabAggregate;

class MarkFoodServedTest extends TabTestCase
{
    public function testCanServePreparedFood()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1, $this->food2])),
            FoodPrepared::of($this->tabId, Collection::make([$this->food1->menuNumber, $this->food2->menuNumber]))
        ])
            ->handle(
                MarkFoodServed::of($this->tabId, Collection::make([$this->food1->menuNumber, $this->food2->menuNumber]))
            );

        $this->assertReleasedEvents($aggregate, [
            FoodServed::of($this->tabId, Collection::make([$this->food1->menuNumber, $this->food2->menuNumber]))
        ]);
    }

    public function testCanNotServeAnUnpreparedFood()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1])),
        ])
            ->handle(
                MarkFoodServed::of($this->tabId, Collection::make([$this->food1->menuNumber]))
            );
    }

    public function testCanNotServeAnPreparedFoodTwice()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1])),
            FoodPrepared::of($this->tabId, Collection::make([$this->food1->menuNumber])),
            FoodServed::of($this->tabId, Collection::make([$this->food1->menuNumber])),
        ])
            ->handle(
                MarkFoodServed::of($this->tabId, Collection::make([$this->food1->menuNumber]))
            );
    }

    public function testCanNotSkipPrepareOrderedAgainFood()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1])),
            FoodPrepared::of($this->tabId, Collection::make([$this->food1->menuNumber])),
            FoodServed::of($this->tabId, Collection::make([$this->food1->menuNumber])),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1])),
        ])
            ->handle(
                MarkFoodServed::of($this->tabId, Collection::make([$this->food1->menuNumber]))
            );
    }
}
