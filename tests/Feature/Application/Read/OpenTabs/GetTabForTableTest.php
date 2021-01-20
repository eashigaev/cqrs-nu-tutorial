<?php

namespace Tests\Feature\Application\Read\OpenTabs;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\OpenTabs\Exceptions\OpenTabNotFound;
use Src\Application\Read\OpenTabs\Queries\GetTabForTable;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;

class GetTabForTableTest extends TestCase
{
    public function testCanGetEmptyTab()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            ])
            ->handle(GetTabForTable::of($this->aTable));

        $this->assertResult($result, [
            'tableNumber' => $this->aTable,
            'waiter' => $this->aWaiter,
            'toServe' => [],
            'inPreparation' => [],
            'served' => []
        ]);
    }

    public function testCanNotGetUnopenedTab()
    {
        $this->expectExceptionObject(OpenTabNotFound::new());

        $this->openTabs
            ->handle(GetTabForTable::of($this->aTable));
    }

    public function testCanNotGetClosedTab()
    {
        $this->expectExceptionObject(OpenTabNotFound::new());

        $this->openTabs
            ->withEvents([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
                TabClosed::of($this->aTabId, 0, 0, 0),
            ])
            ->handle(GetTabForTable::of($this->aTable));
    }

    public function testCanGetTabWithDrinks()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
                DrinksOrdered::of($this->aTabId, Collection::make([$this->drink1, $this->drink2])),
                DrinksServed::of($this->aTabId, Collection::make([$this->drink2->menuNumber]))
            ])
            ->handle(GetTabForTable::of($this->aTable));

        $this->assertResult($result, [
            'tableNumber' => $this->aTable,
            'waiter' => $this->aWaiter,
            'toServe' => [
                [
                    'menuNumber' => $this->drink1->menuNumber,
                    'description' => $this->drink1->description,
                    'price' => $this->drink1->price
                ]
            ],
            'inPreparation' => [],
            'served' => [
                [
                    'menuNumber' => $this->drink2->menuNumber,
                    'description' => $this->drink2->description,
                    'price' => $this->drink2->price
                ]
            ]
        ]);
    }

    public function testCanGetTabWithFood()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
                FoodOrdered::of($this->aTabId, Collection::make([$this->food1, $this->food2, $this->food3])),
                FoodPrepared::of($this->aTabId, Collection::make([$this->food2->menuNumber, $this->food3->menuNumber])),
                FoodServed::of($this->aTabId, Collection::make([$this->food3->menuNumber])),
            ])
            ->handle(GetTabForTable::of($this->aTable));

        $this->assertResult($result, [
            'tableNumber' => $this->aTable,
            'waiter' => $this->aWaiter,
            'toServe' => [
                [
                    'menuNumber' => $this->food2->menuNumber,
                    'description' => $this->food2->description,
                    'price' => $this->food2->price
                ]
            ],
            'inPreparation' => [
                [
                    'menuNumber' => $this->food1->menuNumber,
                    'description' => $this->food1->description,
                    'price' => $this->food1->price
                ]
            ],
            'served' => [
                [
                    'menuNumber' => $this->food3->menuNumber,
                    'description' => $this->food3->description,
                    'price' => $this->food3->price
                ]
            ]
        ]);
    }
}
