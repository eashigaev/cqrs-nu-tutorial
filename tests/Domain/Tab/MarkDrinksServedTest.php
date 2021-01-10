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
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            DrinksOrdered::of($this->tabId, Collection::make([$this->drink1, $this->drink2]))
        ])
            ->handle(
                MarkDrinksServed::of($this->tabId, Collection::make([
                    $this->drink1->menuNumber, $this->drink2->menuNumber
                ]))
            );

        $this->assertReleasedEvents($aggregate, [
            DrinksServed::of($this->tabId, Collection::make([
                $this->drink1->menuNumber, $this->drink2->menuNumber
            ]))
        ]);
    }

    public function testCanNotServeAnUnorderedDrink()
    {
        $this->expectExceptionObject(DrinkNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            DrinksOrdered::of($this->tabId, Collection::make([$this->drink1]))
        ])
            ->handle(
                MarkDrinksServed::of($this->tabId, Collection::make([$this->drink2->menuNumber]))
            );
    }

    public function testCanNotServeAnOrderedDrinksTwice()
    {
        $this->expectExceptionObject(DrinkNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            DrinksOrdered::of($this->tabId, Collection::make([$this->drink1])),
            DrinksServed::of($this->tabId, Collection::make([$this->drink1->menuNumber]))
        ])
            ->handle(
                MarkDrinksServed::of($this->tabId, Collection::make([$this->drink1->menuNumber]))
            );
    }
}
