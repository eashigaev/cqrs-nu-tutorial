<?php

namespace Tests\Domain\Tab;

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
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ])
            ->handle(
                PlaceOrder::of($this->testId, Collection::make([$this->testDrink1, $this->testDrink2]))
            );

        $this->assertReleasedEvents($aggregate, [
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1, $this->testDrink2]))
        ]);
    }

    public function testCanPlaceFoodOrder()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ])
            ->handle(
                PlaceOrder::of($this->testId, Collection::make([$this->testFood1, $this->testFood2]))
            );

        $this->assertReleasedEvents($aggregate, [
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1, $this->testFood2]))
        ]);
    }

    public function testCanPlaceFoodAndDrinksOrder()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ])
            ->handle(
                PlaceOrder::of($this->testId, Collection::make([$this->testFood1, $this->testDrink1]))
            );

        $this->assertReleasedEvents($aggregate, [
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1])),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1]))
        ]);
    }

    public function testCanNotOrderWithUnopenedTab()
    {
        $this->expectExceptionObject(TabNotOpen::new());

        TabAggregate::fromEvents()
            ->handle(
                PlaceOrder::of($this->testId, Collection::make([$this->testDrink1]))
            );
    }

    public function testCanNotOrderWithClosedTab()
    {
        $this->expectExceptionObject(TabNotOpen::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            TabClosed::of($this->testId, 0, 0, 0),
        ])
            ->handle(
                PlaceOrder::of($this->testId, Collection::make([$this->testDrink1]))
            );
    }
}
