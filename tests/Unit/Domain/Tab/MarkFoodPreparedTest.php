<?php

namespace Tests\Unit\Domain\Tab;

use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\MarkFoodPrepared;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\FoodAlreadyPrepared;
use Src\Domain\Tab\Exceptions\FoodNotOutstanding;
use Src\Domain\Tab\TabAggregate;

class MarkFoodPreparedTest extends TestCase
{
    public function testCanPrepareOrderedFood()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            FoodOrdered::of($this->aTabId, Collection::of([$this->food1, $this->food2]))
        ])
            ->handle(
                MarkFoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber]))
            );

        $this->assertReleasedEvents($aggregate, [
            FoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber]))
        ]);
    }

    public function testCanNotPrepareAnUnorderedFood()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            FoodOrdered::of($this->aTabId, Collection::of([$this->food1]))
        ])
            ->handle(
                MarkFoodPrepared::of($this->aTabId, Collection::of([$this->food2->menuNumber]))
            );
    }

    public function testCanNotPrepareAnOrderedFoodTwice()
    {
        $this->expectExceptionObject(FoodAlreadyPrepared::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            FoodOrdered::of($this->aTabId, Collection::of([$this->food1])),
            FoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber]))
        ])
            ->handle(
                MarkFoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber]))
            );
    }
}
