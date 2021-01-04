<?php

namespace Tests\Domain\Tab;

use Codderz\Yoko\Domain\Testing\DomainTestTrait;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\DrinksNotOutstanding;
use Src\Domain\Tab\Exceptions\PaymentNotEnough;
use Src\Domain\Tab\Exceptions\TabAlreadyClosed;
use Src\Domain\Tab\Exceptions\TabAlreadyOpen;
use Src\Domain\Tab\Exceptions\TabHasOutstandingItems;
use Src\Domain\Tab\Exceptions\TabNotOpen;
use Src\Domain\Tab\OrderedItem;
use Src\Domain\Tab\TabAggregate;
use Tests\TestCase;

class TabTest extends TestCase
{
    use DomainTestTrait;

    private Guid $testId;
    private int $testTable;
    private string $testWaiter;

    private OrderedItem $testDrink1, $testDrink2, $testFood1, $testFood2;

    public function setUp(): void
    {
        $this->testId = Guid::of('tab-123');
        $this->testTable = 42;
        $this->testWaiter = 'Derek';

        $this->testDrink1 = OrderedItem::of(4, 'Sprite', true, 5.00);
        $this->testDrink2 = OrderedItem::of(10, 'Beer', true, 3.00);
        $this->testFood1 = OrderedItem::of(16, 'Beef Noodles', false, 10.00);
        $this->testFood2 = OrderedItem::of(25, 'Vegetable Curry', false, 10.00);
    }

    public function testCanOpenTab()
    {
        $aggregate = TabAggregate::fromEvents()
            ->handle(
                OpenTab::of($this->testId, $this->testTable, $this->testWaiter)
            );

        $this->assertReleasedEvents($aggregate, [
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ]);
    }

    public function testCanNotOpenTabTwice()
    {
        $this->expectExceptionObject(TabAlreadyOpen::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ])
            ->handle(
                OpenTab::of($this->testId, $this->testTable, $this->testWaiter)
            );
    }

    public function testCanNotOpenClosedTab()
    {
        $this->expectExceptionObject(TabAlreadyClosed::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            TabClosed::of($this->testId, 0, 0, 0),
        ])
            ->handle(
                OpenTab::of($this->testId, $this->testTable, $this->testWaiter)
            );
    }

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
        $this->expectExceptionObject(DrinksNotOutstanding::new());

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
        $this->expectExceptionObject(DrinksNotOutstanding::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1])),
            DrinksServed::of($this->testId, Collection::make([$this->testDrink1->menuNumber]))
        ])
            ->handle(
                MarkDrinksServed::of($this->testId, Collection::make([$this->testDrink1->menuNumber]))
            );
    }

    public function testCanCloseTabWithTip()
    {
        $aggregate = TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1])),
            DrinksServed::of($this->testId, Collection::make([$this->testDrink1->menuNumber]))
        ])
            ->handle(
                CloseTab::of($this->testId, $this->testDrink1->price + 1.00)
            );

        $this->assertReleasedEvents($aggregate, [
            TabClosed::of($this->testId, $this->testDrink1->price + 1.00, $this->testDrink1->price, 1.00)
        ]);
    }

    public function testCanNotCloseTabWithNotEnoughPayment()
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

    public function testCanNotCloseTabWithOutstandingItems()
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
