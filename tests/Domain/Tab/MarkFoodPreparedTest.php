<?php

namespace Tests\Domain\Tab;

use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\MarkFoodPrepared;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\FoodAlreadyPrepared;
use Src\Domain\Tab\Exceptions\FoodNotOutstanding;
use Src\Domain\Tab\TabAggregate;

class MarkFoodPreparedTest extends TabTestCase
{
    public function testCanPrepareOrderedFood()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1, $this->testFood2]))
        ])
            ->handle(
                MarkFoodPrepared::of($this->testId, Collection::make([$this->testFood1->menuNumber]))
            );

        $this->assertReleasedEvents($aggregate, [
            FoodPrepared::of($this->testId, Collection::make([$this->testFood1->menuNumber]))
        ]);
    }

    public function testCanNotPrepareAnUnorderedFood()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1]))
        ])
            ->handle(
                MarkFoodPrepared::of($this->testId, Collection::make([$this->testFood2->menuNumber]))
            );
    }

    public function testCanNotPrepareAnOrderedFoodTwice()
    {
        $this->expectExceptionObject(FoodAlreadyPrepared::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1])),
            FoodPrepared::of($this->testId, Collection::make([$this->testFood1->menuNumber]))
        ])
            ->handle(
                MarkFoodPrepared::of($this->testId, Collection::make([$this->testFood1->menuNumber]))
            );
    }
}
