<?php

namespace Tests\Unit\Domain\Tab;

use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\DrinkNotOutstanding;
use Src\Domain\Tab\TabAggregate;

class MarkDrinksServedTest extends TestCase
{
    public function testCanServeOrderedDrinks()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            DrinksOrdered::of($this->aTabId, Collection::of([$this->drink1, $this->drink2]))
        ])
            ->markDrinksServed(
                MarkDrinksServed::of($this->aTabId, Collection::of([
                    $this->drink1->menuNumber, $this->drink2->menuNumber
                ]))
            );

        $this->assertReleasedEvents($aggregate, [
            DrinksServed::of($this->aTabId, Collection::of([
                $this->drink1->menuNumber, $this->drink2->menuNumber
            ]))
        ]);
    }

    public function testCanNotServeAnUnorderedDrink()
    {
        $this->expectExceptionObject(DrinkNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            DrinksOrdered::of($this->aTabId, Collection::of([$this->drink1]))
        ])
            ->markDrinksServed(
                MarkDrinksServed::of($this->aTabId, Collection::of([$this->drink2->menuNumber]))
            );
    }

    public function testCanNotServeAnOrderedDrinksTwice()
    {
        $this->expectExceptionObject(DrinkNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            DrinksOrdered::of($this->aTabId, Collection::of([$this->drink1])),
            DrinksServed::of($this->aTabId, Collection::of([$this->drink1->menuNumber]))
        ])
            ->markDrinksServed(
                MarkDrinksServed::of($this->aTabId, Collection::of([$this->drink1->menuNumber]))
            );
    }
}
