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
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            DrinksOrdered::of($this->tabId, Collection::make([$this->drink1])),
            DrinksServed::of($this->tabId, Collection::make([$this->drink1->menuNumber]))
        ])
            ->handle(
                CloseTab::of($this->tabId, $this->drink1->price)
            );

        $this->assertReleasedEvents($aggregate, [
            TabClosed::of($this->tabId, $this->drink1->price, $this->drink1->price, 0.00)
        ]);
    }

    public function testCanCloseTabWithPaymentForFood()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1])),
            FoodPrepared::of($this->tabId, Collection::make([$this->food1->menuNumber])),
            FoodServed::of($this->tabId, Collection::make([$this->food1->menuNumber])),
        ])
            ->handle(
                CloseTab::of($this->tabId, $this->food1->price)
            );

        $this->assertReleasedEvents($aggregate, [
            TabClosed::of($this->tabId, $this->food1->price, $this->food1->price, 0.00)
        ]);
    }

    public function testCanCloseTabWithTip()
    {
        $amountValue = $this->drink1->price + $this->food1->price;

        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            DrinksOrdered::of($this->tabId, Collection::make([$this->drink1])),
            DrinksServed::of($this->tabId, Collection::make([$this->drink1->menuNumber])),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1])),
            FoodPrepared::of($this->tabId, Collection::make([$this->food1->menuNumber])),
            FoodServed::of($this->tabId, Collection::make([$this->food1->menuNumber]))
        ])
            ->handle(
                CloseTab::of($this->tabId, $amountValue + 2.00)
            );

        $this->assertReleasedEvents($aggregate, [
            TabClosed::of($this->tabId, $amountValue + 2.00, $amountValue, 2.00)
        ]);
    }

    public function testCanNotCloseTabWithNotEnoughPaymentForDrinks()
    {
        $this->expectExceptionObject(PaymentNotEnough::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            DrinksOrdered::of($this->tabId, Collection::make([$this->drink1])),
            DrinksServed::of($this->tabId, Collection::make([$this->drink1->menuNumber]))
        ])
            ->handle(
                CloseTab::of($this->tabId, 0)
            );
    }

    public function testCanNotCloseTabWithNotEnoughPaymentForFood()
    {
        $this->expectExceptionObject(PaymentNotEnough::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1])),
            FoodPrepared::of($this->tabId, Collection::make([$this->food1->menuNumber])),
            FoodServed::of($this->tabId, Collection::make([$this->food1->menuNumber])),
        ])
            ->handle(
                CloseTab::of($this->tabId, 0)
            );
    }

    public function testCanNotCloseTabWithOutstandingDrinkItems()
    {
        $this->expectExceptionObject(TabHasOutstandingItems::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            DrinksOrdered::of($this->tabId, Collection::make([$this->drink1])),
        ])
            ->handle(
                CloseTab::of($this->tabId, 0)
            );
    }

    public function testCanNotCloseTabWithOutstandingFoodItems()
    {
        $this->expectExceptionObject(TabHasOutstandingItems::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1, $this->food2])),
        ])
            ->handle(
                CloseTab::of($this->tabId, 0)
            );
    }

    public function testCanNotCloseTabWithPreparedFoodItems()
    {
        $this->expectExceptionObject(TabHasOutstandingItems::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            FoodOrdered::of($this->tabId, Collection::make([$this->food1])),
            FoodPrepared::of($this->tabId, Collection::make([$this->food1->menuNumber])),
        ])
            ->handle(
                CloseTab::of($this->tabId, 0)
            );
    }

    public function testCanNotCloseNotOpenedTab()
    {
        $this->expectExceptionObject(TabNotOpen::new());

        TabAggregate::fromEvents()
            ->handle(
                CloseTab::of($this->tabId, $this->drink1->price + 1.00)
            );
    }

    public function testCanNotCloseTabTwice()
    {
        $this->expectExceptionObject(TabNotOpen::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            TabClosed::of($this->tabId, 0, 0, 0),
        ])
            ->handle(
                CloseTab::of($this->tabId, 0)
            );
    }
}
