<?php

namespace Tests\Domain\Tab;

use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\PaymentNotEnough;
use Src\Domain\Tab\Exceptions\TabHasOutstandingItems;
use Src\Domain\Tab\Exceptions\TabNotOpen;
use Src\Domain\Tab\TabAggregate;

class CloseTabTest extends TabTestCase
{
    public function testCanCloseTabWithPaymentForDrinks()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1])),
            DrinksServed::of($this->testId, Collection::make([$this->testDrink1->menuNumber]))
        ])
            ->handle(
                CloseTab::of($this->testId, $this->testDrink1->price)
            );

        $this->assertReleasedEvents($aggregate, [
            TabClosed::of($this->testId, $this->testDrink1->price, $this->testDrink1->price, 0.00)
        ]);
    }

    public function testCanCloseTabWithPaymentForFood()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1])),
            FoodPrepared::of($this->testId, Collection::make([$this->testFood1->menuNumber])),
            FoodServed::of($this->testId, Collection::make([$this->testFood1->menuNumber])),
        ])
            ->handle(
                CloseTab::of($this->testId, $this->testFood1->price)
            );

        $this->assertReleasedEvents($aggregate, [
            TabClosed::of($this->testId, $this->testFood1->price, $this->testFood1->price, 0.00)
        ]);
    }

    public function testCanCloseTabWithTip()
    {
        $amountValue = $this->testDrink1->price + $this->testFood1->price;

        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1])),
            DrinksServed::of($this->testId, Collection::make([$this->testDrink1->menuNumber])),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1])),
            FoodPrepared::of($this->testId, Collection::make([$this->testFood1->menuNumber])),
            FoodServed::of($this->testId, Collection::make([$this->testFood1->menuNumber]))
        ])
            ->handle(
                CloseTab::of($this->testId, $amountValue + 2.00)
            );

        $this->assertReleasedEvents($aggregate, [
            TabClosed::of($this->testId, $amountValue + 2.00, $amountValue, 2.00)
        ]);
    }

    public function testCanNotCloseTabWithNotEnoughPaymentForDrinks()
    {
        $this->expectExceptionObject(PaymentNotEnough::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1])),
            DrinksServed::of($this->testId, Collection::make([$this->testDrink1->menuNumber]))
        ])
            ->handle(
                CloseTab::of($this->testId, 0)
            );
    }

    public function testCanNotCloseTabWithNotEnoughPaymentForFood()
    {
        $this->expectExceptionObject(PaymentNotEnough::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1])),
            FoodPrepared::of($this->testId, Collection::make([$this->testFood1->menuNumber])),
            FoodServed::of($this->testId, Collection::make([$this->testFood1->menuNumber])),
        ])
            ->handle(
                CloseTab::of($this->testId, 0)
            );
    }

    public function testCanNotCloseTabWithOutstandingDrinkItems()
    {
        $this->expectExceptionObject(TabHasOutstandingItems::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1])),
        ])
            ->handle(
                CloseTab::of($this->testId, 0)
            );
    }

    public function testCanNotCloseTabWithOutstandingFoodItems()
    {
        $this->expectExceptionObject(TabHasOutstandingItems::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1, $this->testFood2])),
        ])
            ->handle(
                CloseTab::of($this->testId, 0)
            );
    }

    public function testCanNotCloseTabWithPreparedFoodItems()
    {
        $this->expectExceptionObject(TabHasOutstandingItems::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            FoodOrdered::of($this->testId, Collection::make([$this->testFood1])),
            FoodPrepared::of($this->testId, Collection::make([$this->testFood1->menuNumber])),
        ])
            ->handle(
                CloseTab::of($this->testId, 0)
            );
    }

    public function testCanNotCloseNotOpenedTab()
    {
        $this->expectExceptionObject(TabNotOpen::new());

        TabAggregate::fromEvents()
            ->handle(
                CloseTab::of($this->testId, $this->testDrink1->price + 1.00)
            );
    }

    public function testCanNotCloseTabTwice()
    {
        $this->expectExceptionObject(TabNotOpen::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            TabClosed::of($this->testId, 0, 0, 0),
        ])
            ->handle(
                CloseTab::of($this->testId, 0)
            );
    }
}
