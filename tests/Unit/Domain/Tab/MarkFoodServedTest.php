<?php

namespace Tests\Unit\Domain\Tab;

use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\MarkFoodServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\FoodNotOutstanding;
use Src\Domain\Tab\TabAggregate;

class MarkFoodServedTest extends TestCase
{
    public function testCanServePreparedFood()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            FoodOrdered::of($this->aTabId, Collection::of([$this->food1, $this->food2])),
            FoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber, $this->food2->menuNumber]))
        ])
            ->markFoodServed(
                MarkFoodServed::of($this->aTabId, Collection::of([$this->food1->menuNumber, $this->food2->menuNumber]))
            );

        $this->assertReleasedEvents($aggregate, [
            FoodServed::of($this->aTabId, Collection::of([$this->food1->menuNumber, $this->food2->menuNumber]))
        ]);
    }

    public function testCanNotServeAnUnpreparedFood()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            FoodOrdered::of($this->aTabId, Collection::of([$this->food1])),
        ])
            ->markFoodServed(
                MarkFoodServed::of($this->aTabId, Collection::of([$this->food1->menuNumber]))
            );
    }

    public function testCanNotServeAnPreparedFoodTwice()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            FoodOrdered::of($this->aTabId, Collection::of([$this->food1])),
            FoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber])),
            FoodServed::of($this->aTabId, Collection::of([$this->food1->menuNumber])),
        ])
            ->markFoodServed(
                MarkFoodServed::of($this->aTabId, Collection::of([$this->food1->menuNumber]))
            );
    }

    public function testCanNotSkipPrepareOrderedAgainFood()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            FoodOrdered::of($this->aTabId, Collection::of([$this->food1])),
            FoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber])),
            FoodServed::of($this->aTabId, Collection::of([$this->food1->menuNumber])),
            FoodOrdered::of($this->aTabId, Collection::of([$this->food1])),
        ])
            ->markFoodServed(
                MarkFoodServed::of($this->aTabId, Collection::of([$this->food1->menuNumber]))
            );
    }
}
