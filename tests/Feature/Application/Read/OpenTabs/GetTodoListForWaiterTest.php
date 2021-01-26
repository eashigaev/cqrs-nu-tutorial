<?php

namespace Tests\Feature\Application\Read\OpenTabs;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\OpenTabs\Queries\GetTodoListForWaiter;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabOpened;

class GetTodoListForWaiterTest extends TestCase
{
    public function testCanGetTodoListForWaiter()
    {
        $result = $this
            ->openTabs([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
                DrinksOrdered::of($this->aTabId, Collection::of([$this->drink1, $this->drink2])),
                DrinksServed::of($this->aTabId, Collection::of([$this->drink1->menuNumber])),
                FoodOrdered::of($this->aTabId, Collection::of([$this->food1, $this->food2, $this->food3])),
                FoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber, $this->food2->menuNumber])),
                FoodServed::of($this->aTabId, Collection::of([$this->food1->menuNumber])),
            ])
            ->getTodoListForWaiter(GetTodoListForWaiter::of($this->aWaiter));

        $this->assertResult($result, [
            $this->aTable => [
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
        $result = $this
            ->openTabs([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
                DrinksOrdered::of($this->aTabId, Collection::of([$this->drink1])),
                FoodOrdered::of($this->aTabId, Collection::of([$this->food1])),
                FoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber])),
            ])
            ->getTodoListForWaiter(GetTodoListForWaiter::of($this->bWaiter));

        $this->assertResult($result, []);
    }

    public function testMustGetTableListsOnlyWhenItemsToServe()
    {
        $result = $this
            ->openTabs([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            ])
            ->getTodoListForWaiter(GetTodoListForWaiter::of($this->aWaiter));

        $this->assertResult($result, []);
    }

    public function testMustGetEmptyTodoListWhenNoTabs()
    {
        $result = $this
            ->openTabs()
            ->getTodoListForWaiter(GetTodoListForWaiter::of($this->aWaiter));

        $this->assertResult($result, []);
    }
}
