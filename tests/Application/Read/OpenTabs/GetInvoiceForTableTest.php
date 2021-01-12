<?php

namespace Tests\Application\Read\OpenTabs;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\OpenTabs\Exceptions\OpenTabNotFound;
use Src\Application\Read\OpenTabs\Queries\GetInvoiceForTable;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;

class GetInvoiceForTableTest extends OpenTabsTestCase
{
    public function testCanGetEmptyTabInvoice()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->waiter),
            ])
            ->handle(GetInvoiceForTable::of($this->table1));

        $this->assertResult($result, [
            'tabId' => $this->tabId1->value,
            'tableNumber' => $this->table1,
            'items' => [],
            'total' => 0,
            'hasUnservedItems' => false
        ]);
    }

    public function testCanNotGetUnopenedTabInvoice()
    {
        $this->expectExceptionObject(OpenTabNotFound::new());

        $this->openTabs
            ->handle(GetInvoiceForTable::of($this->table1));
    }

    public function testCanNotGetClosedTabInvoice()
    {
        $this->expectExceptionObject(OpenTabNotFound::new());

        $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->waiter),
                TabClosed::of($this->tabId1, 0, 0, 0),
            ])
            ->handle(GetInvoiceForTable::of($this->table1));
    }

    public function testCanGetTabInvoiceForDrinks()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->waiter),
                DrinksOrdered::of($this->tabId1, Collection::make([$this->drink1, $this->drink2])),
                DrinksServed::of($this->tabId1, Collection::make([$this->drink1->menuNumber]))
            ])
            ->handle(GetInvoiceForTable::of($this->table1));

        $this->assertResult($result, [
            'tabId' => $this->tabId1->value,
            'tableNumber' => $this->table1,
            'items' => [
                [
                    'menuNumber' => $this->drink1->menuNumber,
                    'description' => $this->drink1->description,
                    'price' => $this->drink1->price
                ]
            ],
            'total' => $this->drink1->price,
            'hasUnservedItems' => true
        ]);
    }

    public function testCanGetTabInvoiceForFood()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->waiter),
                FoodOrdered::of($this->tabId1, Collection::make([$this->food1, $this->food2, $this->food3])),
                FoodPrepared::of($this->tabId1, Collection::make([$this->food1->menuNumber, $this->food3->menuNumber])),
                FoodServed::of($this->tabId1, Collection::make([$this->food3->menuNumber]))
            ])
            ->handle(GetInvoiceForTable::of($this->table1));

        $this->assertResult($result, [
            'tabId' => $this->tabId1->value,
            'tableNumber' => $this->table1,
            'items' => [
                [
                    'menuNumber' => $this->food3->menuNumber,
                    'description' => $this->food3->description,
                    'price' => $this->food3->price
                ]
            ],
            'total' => $this->food3->price,
            'hasUnservedItems' => true
        ]);
    }
}
