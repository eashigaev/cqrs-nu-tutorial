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
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1, $this->testFood2])),
            FoodPrepared::of($this->testId, Collection::make([
                $this->testFood1->menuNumber, $this->testFood2->menuNumber
            ]))
        ])
            ->handle(
                MarkFoodServed::of($this->testId, Collection::make([
                    $this->testFood1->menuNumber, $this->testFood2->menuNumber
                ]))
            );

        $this->assertReleasedEvents($aggregate, [
            FoodServed::of($this->testId, Collection::make([
                $this->testFood1->menuNumber, $this->testFood2->menuNumber
            ]))
        ]);
    }

    public function testCanNotServeAnUnpreparedFood()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1])),
        ])
            ->handle(
                MarkFoodServed::of($this->testId, Collection::make([$this->testFood1->menuNumber]))
            );
    }

    public function testCanNotServeAnPreparedFoodTwice()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1])),
            FoodPrepared::of($this->testId, Collection::make([$this->testFood1->menuNumber])),
            FoodServed::of($this->testId, Collection::make([$this->testFood1->menuNumber])),
        ])
            ->handle(
                MarkFoodServed::of($this->testId, Collection::make([$this->testFood1->menuNumber]))
            );
    }

    public function testCanNotSkipPrepareOrderedAgainFood()
    {
        $this->expectExceptionObject(FoodNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1])),
            FoodPrepared::of($this->testId, Collection::make([$this->testFood1->menuNumber])),
            FoodServed::of($this->testId, Collection::make([$this->testFood1->menuNumber])),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1])),
        ])
            ->handle(
                MarkFoodServed::of($this->testId, Collection::make([$this->testFood1->menuNumber]))
            );
    }
}
