<?php

namespace Tests\Domain\Tab;

use Codderz\Yoko\Domain\Testing\DomainTestTrait;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\DrinksNotOutstanding;
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

    public function testOpenTab()
    {
        $aggregate = TabAggregate::fromEvents()
            ->handle(
                OpenTab::of($this->testId, $this->testTable, $this->testWaiter)
            );

        $this->assertReleasedEvents($aggregate, [
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ]);
    }

    public function testCanNotOrderWithUnopenedTab()
    {
        $this->expectExceptionObject(TabNotOpen::of());

        TabAggregate::fromEvents()
            ->handle(
                PlaceOrder::of($this->testId, Collection::make([$this->testDrink1]))
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

    public function testCanPlaceFoodAndDrinkOrder()
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

    public function testOrderedDrinksCanBeServed()
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
        $this->expectExceptionObject(DrinksNotOutstanding::of());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1]))
        ])
            ->handle(
                MarkDrinksServed::of($this->testId, Collection::make([$this->testDrink2->menuNumber]))
            );
    }

    public function testCanNotServeAnOrderedDrinkTwice()
    {
        $this->expectExceptionObject(DrinksNotOutstanding::of());

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
