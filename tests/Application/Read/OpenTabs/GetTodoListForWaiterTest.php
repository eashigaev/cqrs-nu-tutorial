<?php

namespace Tests\Application\Read\OpenTabs;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\OpenTabs\Queries\GetTodoListForWaiter;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabOpened;

class GetTodoListForWaiterTest extends OpenTabsTestCase
{
    public function testCanGetTodoListForWaiter()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->waiter1),
                DrinksOrdered::of($this->tabId1, Collection::make([$this->drink1, $this->drink2])),
                DrinksServed::of($this->tabId1, Collection::make([$this->drink1->menuNumber])),
                FoodOrdered::of($this->tabId1, Collection::make([$this->food1, $this->food2, $this->food3])),
                FoodPrepared::of($this->tabId1, Collection::make([$this->food1->menuNumber, $this->food2->menuNumber])),
                FoodServed::of($this->tabId1, Collection::make([$this->food1->menuNumber])),
            ])
            ->handle(GetTodoListForWaiter::of($this->waiter1));

        $this->assertResult($result, [
            $this->table1 => [
                [
                    'menuNumber' => $this->drink2->menuNumber,
                    'description' => $this->drink2->description,
                    'price' => $this->drink2->price
                ],
                [
                    'menuNumber' => $this->food2->menuNumber,
                    'description' => $this->food2->description,
                    'price' => $this->food2->price
                ]
            ]
        ]);
    }

    public function testMustGetEmptyTodoListForFreeWaiter()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->waiter1),
                DrinksOrdered::of($this->tabId1, Collection::make([$this->drink1])),
                FoodOrdered::of($this->tabId1, Collection::make([$this->food1])),
                FoodPrepared::of($this->tabId1, Collection::make([$this->food1->menuNumber])),
            ])
            ->handle(GetTodoListForWaiter::of($this->waiter2));

        $this->assertResult($result, []);
    }

    public function testMustGetTableListsOnlyWhenItemsToServe()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->waiter1),
            ])
            ->handle(GetTodoListForWaiter::of($this->waiter1));

        $this->assertResult($result, []);
    }

    public function testMustGetEmptyTodoListWhenNoTabs()
    {
        $result = $this->openTabs
            ->handle(GetTodoListForWaiter::of($this->waiter1));

        $this->assertResult($result, []);
    }
}
