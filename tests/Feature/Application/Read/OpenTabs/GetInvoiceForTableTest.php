<?php

namespace Tests\Feature\Application\Read\OpenTabs;

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

class GetInvoiceForTableTest extends TestCase
{
    public function testCanGetEmptyTabInvoice()
    {
        $result = $this
            ->openTabs([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            ])
            ->getInvoiceForTable(GetInvoiceForTable::of($this->aTable));

        $this->assertResult($result, [
            'tabId' => $this->aTabId->value,
            'tableNumber' => $this->aTable,
            'items' => [],
            'total' => 0,
            'hasUnservedItems' => false
        ]);
    }

    public function testCanNotGetUnopenedTabInvoice()
    {
        $this->expectExceptionObject(OpenTabNotFound::new());

        $this
            ->openTabs()
            ->getInvoiceForTable(GetInvoiceForTable::of($this->aTable));
    }

    public function testCanNotGetClosedTabInvoice()
    {
        $this->expectExceptionObject(OpenTabNotFound::new());

        $this
            ->openTabs([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
                TabClosed::of($this->aTabId, 0, 0, 0),
            ])
            ->getInvoiceForTable(GetInvoiceForTable::of($this->aTable));
    }

    public function testCanGetTabInvoiceForDrinks()
    {
        $result = $this
            ->openTabs([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
                DrinksOrdered::of($this->aTabId, Collection::make([$this->drink1, $this->drink2])),
                DrinksServed::of($this->aTabId, Collection::make([$this->drink1->menuNumber]))
            ])
            ->getInvoiceForTable(GetInvoiceForTable::of($this->aTable));

        $this->assertResult($result, [
            'tabId' => $this->aTabId->value,
            'tableNumber' => $this->aTable,
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
        $result = $this
            ->openTabs([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
                FoodOrdered::of($this->aTabId, Collection::make([$this->food1, $this->food2, $this->food3])),
                FoodPrepared::of($this->aTabId, Collection::make([$this->food1->menuNumber, $this->food3->menuNumber])),
                FoodServed::of($this->aTabId, Collection::make([$this->food3->menuNumber]))
            ])
            ->getInvoiceForTable(GetInvoiceForTable::of($this->aTable));

        $this->assertResult($result, [
            'tabId' => $this->aTabId->value,
            'tableNumber' => $this->aTable,
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
