<?php

namespace Tests\Domain\Tab;

use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\DrinkNotOutstanding;
use Src\Domain\Tab\TabAggregate;

class MarkDrinksServedTest extends TabTestCase
{
    public function testCanServeOrderedDrinks()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1, $this->testDrink2]))
        ])
            ->handle(
                MarkDrinksServed::of($this->testId, Collection::make([
                    $this->testDrink1->menuNumber, $this->testDrink2->menuNumber
                ]))
            );

        $this->assertReleasedEvents($aggregate, [
            DrinksServed::of($this->testId, Collection::make([
                $this->testDrink1->menuNumber, $this->testDrink2->menuNumber
            ]))
        ]);
    }

    public function testCanNotServeAnUnorderedDrink()
    {
        $this->expectExceptionObject(DrinkNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1]))
        ])
            ->handle(
                MarkDrinksServed::of($this->testId, Collection::make([$this->testDrink2->menuNumber]))
            );
    }

    public function testCanNotServeAnOrderedDrinksTwice()
    {
        $this->expectExceptionObject(DrinkNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1])),
            DrinksServed::of($this->testId, Collection::make([$this->testDrink1->menuNumber]))
        ])
            ->handle(
                MarkDrinksServed::of($this->testId, Collection::make([$this->testDrink1->menuNumber]))
            );
    }
}
