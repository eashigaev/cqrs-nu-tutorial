<?php

namespace Tests\Unit\Domain\Tab;

use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\TabNotOpen;
use Src\Domain\Tab\TabAggregate;

class PlaceOrderTest extends TabTestCase
{
    public function testCanPlaceDrinksOrder()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter)
        ])
            ->handle(
                PlaceOrder::of($this->tabId, Collection::make([$this->drink1, $this->drink2]))
            );

        $this->assertReleasedEvents($aggregate, [
            DrinksOrdered::of($this->tabId, Collection::make([$this->drink1, $this->drink2]))
        ]);
    }

    public function testCanPlaceFoodOrder()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter)
        ])
            ->handle(
                PlaceOrder::of($this->tabId, Collection::make([$this->food1, $this->food2]))
            );

        $this->assertReleasedEvents($aggregate, [
            FoodOrdered::of($this->tabId, Collection::make([$this->food1, $this->food2]))
        ]);
    }

    public function testCanPlaceFoodAndDrinksOrder()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter)
        ])
            ->handle(
                PlaceOrder::of($this->tabId, Collection::make([$this->food1, $this->drink1]))
            );

        $this->assertReleasedEvents($aggregate, [
            DrinksOrdered::of($this->tabId, Collection::make([$this->drink1])),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1]))
        ]);
    }

    public function testCanNotOrderWithUnopenedTab()
    {
        $this->expectExceptionObject(TabNotOpen::new());

        TabAggregate::fromEvents()
            ->handle(
                PlaceOrder::of($this->tabId, Collection::make([$this->drink1]))
            );
    }

    public function testCanNotOrderWithClosedTab()
    {
        $this->expectExceptionObject(TabNotOpen::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            TabClosed::of($this->tabId, 0, 0, 0),
        ])
            ->handle(
                PlaceOrder::of($this->tabId, Collection::make([$this->drink1]))
            );
    }
}
