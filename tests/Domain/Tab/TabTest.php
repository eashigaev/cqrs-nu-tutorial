<?php

namespace Tests\Domain\Tab;

use Codderz\Yoko\Domain\Testing\DomainTest;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\TabNotOpen;
use Src\Domain\Tab\OrderedItem;
use Src\Domain\Tab\TabAggregate;
use Tests\TestCase;

class TabTest extends TestCase
{
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
        DomainTest::given(TabAggregate::fromEvents())
            ->when([OpenTab::of($this->testId, $this->testTable, $this->testWaiter)])
            ->then([TabOpened::of($this->testId, $this->testTable, $this->testWaiter)]);
    }

    public function testCanNotOrderWithUnopenedTab()
    {
        DomainTest::given(TabAggregate::fromEvents())
            ->when([PlaceOrder::of($this->testId, Collection::make([$this->testDrink1]))])
            ->thenFail(TabNotOpen::of());
    }

    public function testCanPlaceDrinksOrder()
    {
        DomainTest::given(TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ]))
            ->when([PlaceOrder::of($this->testId, Collection::make([$this->testDrink1, $this->testDrink2]))])
            ->then([DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1, $this->testDrink2]))]);
    }

    public function testCanPlaceFoodOrder()
    {
        DomainTest::given(TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ]))
            ->when([PlaceOrder::of($this->testId, Collection::make([$this->testFood1, $this->testFood2]))])
            ->then([FoodOrdered::of($this->testId, Collection::make([$this->testFood1, $this->testFood2]))]);
    }

    public function testCanPlaceFoodAndDrinkOrder()
    {
        DomainTest::given(TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ]))
            ->when([PlaceOrder::of($this->testId, Collection::make([$this->testFood1, $this->testDrink1]))])
            ->then([
                DrinksOrdered::of($this->testId, Collection::make([$this->testDrink1])),
                FoodOrdered::of($this->testId, Collection::make([$this->testFood1]))
            ]);
    }


}
